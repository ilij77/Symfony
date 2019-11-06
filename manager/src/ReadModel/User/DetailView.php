<?php


namespace App\ReadModel\User;


class DetailView
{
	public $id;
	public $date;
	public $first_name;
	public $last_name;
	public $email;
	public $role;
	public $status;
	/**
	 * *@var  NetworkView[]
	 */
	public $networks;

}