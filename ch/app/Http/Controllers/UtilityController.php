<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App;
use DB;
use Config;
use View;
/*use PHPMailerAutoload;
use PHPMailer;*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Cookie\CookieJar;

class UtilityController extends Controller {
	public function getCountriesList( $countryId = 0 ) {

		$DBTables = Config::get( 'constants.DbTables' );

		$countriesAry = DB::table( $DBTables['Countries'] )
		                  ->where( 'id', ( $countryId > 0 ? '=' : '>' ), (int) $countryId )
		                  ->get();

		return $countriesAry;

	}

	public function getStatesList( $stateid = 0 ) {
		$DBTables = Config::get( 'constants.DbTables' );
		$statesAry = DB::table( $DBTables['States'] )
		               ->where( 'id', ( $stateid > 0 ? '=' : '>' ), (int) $stateid )
		               ->get();

		return $statesAry;

	}

	public function createEmail( $email_template = '', $replace_ary = '', $to = '', $subject = '', $pdf = '' ) {
		$emailCMSAry = $this->getEmailTemplateDetailsByTitle( $email_template );

		ob_start();

		$message = ob_get_clean();

		if ( count( $emailCMSAry ) == 0 ) {
			return false;
		} else {
			if ( trim( $subject ) == '' ) {
				$subject = $emailCMSAry[0]->subject;
			} else {
				$subject = $subject;
			}
			$message .= $emailCMSAry[0]->sectionDescription;
		}


		if ( count( $replace_ary ) > 0 ) {
			foreach ( $replace_ary as $replace_key => $replace_value ) {
				$message = str_replace( $replace_key, $replace_value, $message );
				$subject = str_replace( $replace_key, $replace_value, $subject );
			}
		}

		ob_start();

		$message .= ob_get_clean();

		$supportEmail = Config::get( 'constants.customerSupportEmail' );

		$this->sendEmail( $to, $supportEmail, $subject, $message, $pdf );

	}

	public function getEmailTemplateDetailsByTitle( $title = '' ) {
		$DBTables    = Config::get( 'constants.DbTables' );
		$emailCMSAry = DB::table( $DBTables['Email_CMS'] )
		                 ->where( 'sectionTitle', '=', trim( $title ) )
		                 ->select( '*' )->get();

		return $emailCMSAry;
	}

	public function sendEmail( $to = '', $from = '', $subject = '', $message = '', $attach_file = '' ) {


        $mail = new PHPMailer( true );
        $mail->isSMTP(); // tell to use smtp
        $mail->CharSet    = "utf-8"; // set charset to utf8
        $mail->SMTPAuth   = false;  // use smpt auth
        /*$mail->SMTPSecure = "tls"; // or ssl
        $mail->Host       = "smtp.gmail.com";
        $mail->Port       = 587;
        $mail->Username   = "whiz.solutions.mails@gmail.com";
        $mail->Password   = "aniltest";*/
        $mail->setFrom( $from, "Golf Club Hire" );
        $mail->Subject = $subject;
        $mail->MsgHTML( $message );
        $mail->addAddress( $to, "Club Hire Member" );
//        $mail->addAddress( 'prashant12it@gmail.com', "Club Hire Member" );

        try {
            $mail->Send();
            $mail_sent = true;
        } catch ( Exception $e ) {
            $mail_sent = false;
        }
//        dd($mail_sent);die;
		if ( $mail_sent ) {
			$success = 1;
		} else {
			$success = 0;
		}

		$logDataAry = array(
			'szEmailBody'    => $message,
			'szEmailSubject' => $subject,
			'szToAddress'    => $to,
			'created_at'     => date( 'Y-m-d H:i:s' ),
			'iSuccess'       => $success
		);
		$this->logEmails( $logDataAry );

		return $success;
	}

	public function logEmails( $data = array() ) {
		$DBTables = Config::get( 'constants.DbTables' );
		DB::table( $DBTables['Email_Log'] )->insert( $data );
	}

	public function checkPartnerAlreadyLogin( $request ) {

		$partnerSession = session()->get( "partner_credn" );

		$partner_arr = array();
		$validLogin  = false;
		if ( $partnerSession ) {
			$partner_arr['idPartner'] = (int) $partnerSession['idPartner'];

		} else if ( $request->cookie( 'GLH_PARTNER_CK' ) ) {
			$partnerCookie = $request->cookie( 'GLH_PARTNER_CK' );
			$encKey1       = "#17GCH#77#";
			$encKey2       = "#PCH2017#GcHpl#";

			$decryptedC1 = base64_decode( $partnerCookie );
			$decryptedC2 = preg_replace( "/$encKey1/", "", $decryptedC1 );
			$decryptedC3 = preg_replace( "/$encKey2/", "", $decryptedC2 );

			list( $partner_arr['idPartner'], $partner_arr['partnerEmail'] ) = explode( "~", $decryptedC3 );
		}

		if ( ! empty( $partner_arr ) ) {
			if ( (int) $partner_arr['idPartner'] > 0 ) {
				$DBTables = Config::get( 'constants.DbTables' );
				$partner  = DB::table( $DBTables['Partners'] )
				              ->where( 'id', '=', (int) $partner_arr['idPartner'] )
				              ->select( 'id', 'name', 'email', 'iActive' )
				              ->get();
				if ( count( $partner ) > 0 ) {
					if ( (int) $partner[0]->iActive == 1 ) {
						$partnerSession                 = array();
						$partnerSession['idPartner']    = $partner[0]->id;
						$partnerSession['partnerName']  = $partner[0]->name;
						$partnerSession['partnerEmail'] = $partner[0]->email;
						session()->put( 'partner_credn', $partnerSession );
						$validLogin = true;
					}
				}
			}
		}

		return $validLogin;
	}

	public function getRealIpAddr() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) )   //check ip from share internet
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )   //to check ip is pass from proxy
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	public function ip_info( $ip = null, $purpose = "location", $deep_detect = true ) {
		$output = null;
		if ( filter_var( $ip, FILTER_VALIDATE_IP ) === false ) {
			$ip = $_SERVER["REMOTE_ADDR"];
			if ( $deep_detect ) {
				if ( filter_var( @$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP ) ) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				}
				if ( filter_var( @$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP ) ) {
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				}
			}
		}
		$purpose    = str_replace( array( "name", "\n", "\t", " ", "-", "_" ), null, strtolower( trim( $purpose ) ) );
		$support    = array( "country", "countrycode", "state", "region", "city", "location", "address" );
		$continents = array(
			"AF" => "Africa",
			"AN" => "Antarctica",
			"AS" => "Asia",
			"EU" => "Europe",
			"OC" => "Australia (Oceania)",
			"NA" => "North America",
			"SA" => "South America"
		);
		if ( filter_var( $ip, FILTER_VALIDATE_IP ) && in_array( $purpose, $support ) ) {
			$ipdat = @json_decode( file_get_contents( "http://www.geoplugin.net/json.gp?ip=" . $ip ) );
			if ( @strlen( trim( $ipdat->geoplugin_countryCode ) ) == 2 ) {
				switch ( $purpose ) {
					case "location":
						$output = array(
							"city"           => @$ipdat->geoplugin_city,
							"state"          => @$ipdat->geoplugin_regionName,
							"country"        => @$ipdat->geoplugin_countryName,
							"country_code"   => @$ipdat->geoplugin_countryCode,
							"continent"      => @$continents[ strtoupper( $ipdat->geoplugin_continentCode ) ],
							"continent_code" => @$ipdat->geoplugin_continentCode
						);
						break;
					case "address":
						$address = array( $ipdat->geoplugin_countryName );
						if ( @strlen( $ipdat->geoplugin_regionName ) >= 1 ) {
							$address[] = $ipdat->geoplugin_regionName;
						}
						if ( @strlen( $ipdat->geoplugin_city ) >= 1 ) {
							$address[] = $ipdat->geoplugin_city;
						}
						$output = implode( ", ", array_reverse( $address ) );
						break;
					case "city":
						$output = @$ipdat->geoplugin_city;
						break;
					case "state":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "region":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "country":
						$output = @$ipdat->geoplugin_countryName;
						break;
					case "countrycode":
						$output = @$ipdat->geoplugin_countryCode;
						break;
				}
			}
		}

		return $output;
	}
}
