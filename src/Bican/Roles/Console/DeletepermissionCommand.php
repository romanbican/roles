<?php namespace Bican\Roles\Console;

use Bican\Roles\Models\Permission;
use Illuminate\Console\Command;

class DeletepermissionCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'roles:deletepermission {--id=} {--slug=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete a permission from the database';

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
		$permissionModel = config('roles.models.permission');
		if((int)$this->option("id") > 0) {
			$model = $permissionModel::find((int)$this->option("id"));
		} elseif($this->option("slug") != "") {
			$model = $permissionModel::where("slug",$this->option("slug"))->first();
		} else {
			$this->error("No id or slug option specified. Please use --id= or --slug=");
			return;
		}

		if(!$model instanceof Permission) {
			$this->error("Permission with id or slug specified not found.");
			return;
		}

		$name = $model->name;

		if(!$model->delete()) {
			$this->error("Failed to delete the permission");
			return;
		}

		$this->info("Permission '".$name."'' deleted succesfully");
	}
}
