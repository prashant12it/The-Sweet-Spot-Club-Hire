<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 10-11-2017
 * Time: 04:22 PM
 */
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use App;
use App\Product;
use App\Http\Controllers\CustomerOrderController;
use DB;
use Config;
use View;
use Session;
use Cookie;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Weblee\Mandrill\Mail;
class SendMandrillMail extends Controller
{
    private $mandrill;
    public function __construct(Mail $mandrill)
    {
        $this->mandrill = $mandrill;
    }

    public function sendMandrilMail($data)
    {

        try{
            $template_content[] = array(
                'name' => 'USER',
                'content' => $data['username']
            );
            $template_content[] = array(
                'name' => 'ORDER_DETAILS',
                'content' => $data['orderDetail']
            );
            $template_content[] = array(
                'name' => 'COMPANY',
                'content' => 'The Sweet Spot Club Hire'
            );
            $to_addresses = array();
            $to_addresses[0]['name'] = $data['username'];
            $to_addresses[0]['email'] = $data['useremail'];
            $to_addresses[0]['type'] = 'to';
            $message = array(
                'subject' => $data['subject'],
                'html' => $data['htmlmessage'],
                'from_email' => 'lukecerra@tssclubhire.com',
                'to' => $to_addresses,
                'headers' => array('Reply-To' => 'lukecerra@tssclubhire.com'),
                'track_opens' => true,
                'track_clicks' => true,
                'auto_text' => true,
                'url_strip_qs' => true,
                'preserve_recipients' => true,
                'merge' => true,
                'merge_language' => 'mailchimp',
                'global_merge_vars' => $template_content,
            );
            $result = $this->mandrill->messages()->sendTemplate($data['templateName'], $template_content, $message);
            if($result[0]['status'] != 'sent')
            {
                file_put_contents('../mandrill-fail.txt', "Following email has been ".$result[0]['status']."-\nSubject: ".$message['subject']."\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: ".$message['from_email']."\nErorr: ".$result[0]['reject_reason']."\nTime: ".date("m/d/Y h:i:s A")."\n\n");
//        return true;
                dd($result);
                // log mandrill erorr
//        write_log("mandrill/log", "Following email has been {$result[0]['status']}-\nSubject: $subject\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: $from_email\nErorr: ".$result[0]['reject_reason']."\nTime: ".date("m/d/Y h:i:s A")."\n\n");
            }
            else
            {
                file_put_contents('../mandrill-success.txt', "Following email has been sent successfully-\nSubject: ".$message['subject']."\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: ".$message['from_email']."\nTime: ".date("m/d/Y h:i:s A")."\n\n");
                dd('Sent successfully.');
                //write_log("mandrill/log", "Following email has been sent successfully-\nSubject: $subject\nTo: {$to_addresses[0]['name']} <{$to_addresses[0]['email']}>\nFrom: $from_email\nTime: ".date("m/d/Y h:i:s A")."\n\n");
            }
        } catch(Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            //echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
            //throw $e;

            // log mandrill erorr
            file_put_contents('../mandrill-fail.txt', date("m/d/Y h:i:s A") . "Error: " . $e->getMessage() . "\n\n");
            dd($e->getMessage());
        }

//        $mandrill->messages()->sendTemplate($data);
	}
}