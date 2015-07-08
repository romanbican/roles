<?php namespace Bican\Roles\Console;

use Bican\Roles\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class AddpermissionCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'roles:addpermission {name} {slug?} {description?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Add a permission to the database';

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
		$data = [
			'name' => $this->argument("name"),
			'slug' => Str::slug(($this->argument("slug") ?: strtolower($this->argument("name"))), config('roles.separator')),
		];

		if($this->argument("description") != "") {
			$data['description'] = $this->argument("description");
		}

		$permissionModel = config('roles.models.permission');
		if($permissionModel::where("name",$data['name'])->orWhere("slug",$data['slug'])->count() > 0) {
			$this->error("A permission with the same name or slug already exists");
			return;
		}

		try {
			$newModel = $permissionModel::create($data);
		} catch (QueryException $e) {
			$this->error("Failed to create the permission: \n".$e->getMessage());
			return;
		}

		if(!$newModel instanceof Permission) {
			$this->error("Failed to create the permission");
		}

		$this->info("Permission ".$data['name']." created succesfully");
	}
}
