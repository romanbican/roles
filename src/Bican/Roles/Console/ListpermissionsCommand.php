<?php namespace Bican\Roles\Console;

use Bican\Roles\Models\Permission;
use Illuminate\Console\Command;

class ListpermissionsCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'roles:listpermissions';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List the permissions in the database';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$headers = ["Id","Name","Slug","Description"];
		$permissionModel = config('roles.models.permission');
		$permissions = $permissionModel::all(['id','name','slug','description']);
		$this->table($headers, $permissions);
	}
}
