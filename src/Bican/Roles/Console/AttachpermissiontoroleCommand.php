<?php namespace Bican\Roles\Console;

use Bican\Roles\Models\Permission;
use Illuminate\Console\Command;

class AttachpermissiontoroleCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'roles:attachpermissiontorole {--detach-all} {--detach} {roleIdOrSlug} {permissionIdOrSlug?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Attach or detach a permission to/from a role';

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
		$roleModel = config('roles.models.role');

		if((int)$this->argument("roleIdOrSlug") > 0 && (int)$this->argument("roleIdOrSlug") == $this->argument("roleIdOrSlug")) {
			$role = $roleModel::find((int)$this->argument("roleIdOrSlug"));
		} elseif($this->argument("roleIdOrSlug") != "") {
			$role = $roleModel::where("slug",$this->argument("roleIdOrSlug"))->first();
		} else {
			$this->error("Invalid or empty role id or slug");
			return;
		}

		if($this->option("detach-all")) {
			$role->detachAllPermissions();
			$this->info("All roles detached from role '".$role->name."' succesfully");
			return;
		}

		if((int)$this->argument("permissionIdOrSlug") > 0 && (int)$this->argument("permissionIdOrSlug") == $this->argument("permissionIdOrSlug")) {
			$permission = $permissionModel::find((int)$this->argument("permissionIdOrSlug"));
		} elseif($this->argument("permissionIdOrSlug") != "") {
			$permission = $permissionModel::where("slug",$this->argument("permissionIdOrSlug"))->first();
		} else {
			$this->error("Invalid or empty permission id or slug");
			return;
		}

		if(!$permission instanceof Permission) {
			$this->error("Permission with id or slug specified not found.");
			return;
		}

		if($this->option('detach')) {
			$role->detachPermission($permission);

			$this->info("Permission '".$permission->name."' detached from role '".$role->name."' succesfully");
		} else {
			$role->attachPermission($permission);

			$this->info("Permission '".$permission->name."' attacched to role '".$role->name."' succesfully");
		}
	}
}
