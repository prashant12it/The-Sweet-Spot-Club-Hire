<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailChimpController extends Controller
{
	public $mailchimp;
	public $listId = 'f344453023';

	public function __construct(\Mailchimp $mailchimp)
	{
		$this->mailchimp = $mailchimp;
	}

	public function subscribe(Request $request)
	{

		try {


			$this->mailchimp
				->lists
				->subscribe(
					$this->listId,
					['email' => $request->input('buyer_email')]
				);

//			return redirect()->back()->with('success','Email Subscribed successfully');
return true;
		} catch (\Mailchimp_List_AlreadySubscribed $e) {
			return redirect()->back()->with('error','Email is Already Subscribed');
		} catch (\Mailchimp_Error $e) {
			return redirect()->back()->with('error','Error from MailChimp');
		}
	}
}
