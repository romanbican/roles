<?php namespace Bican\Roles\Console;

use Illuminate\Console\Command;
use Bican\Roles\Models\Role;

class ListrolesCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'roles:listroles';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List the roles in the database';

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
		$headers = ["Id","Name","Slug","Level","Description"];
		$roleModel = config('roles.models.role');
		$roles = $roleModel::all(['id','name','slug','level','description']);
		$this->table($headers, $roles);
	}
}
