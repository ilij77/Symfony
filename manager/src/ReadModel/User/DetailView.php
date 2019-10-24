<?php


namespace App\ReadModel\User;


class DetailView
{
	public $id;
	public $date;
	public $email;
	public $role;
	public $status;
	/**
	 * *@var  NetworkView[]
	 */
	public $networks;

}