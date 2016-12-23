<?php namespace Bican\Roles\Console;

use Bican\Roles\Models\Permission;
use Illuminate\Console\Command;

class AttachpermissiontouserCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'roles:attachpermissiontouser {--detach-all} {--detach} {userId} {permissionIdOrSlug?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Attach or detach a permission to/from a user';

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
		$userModel = config('auth.model');

		$user = $userModel::find($this->argument("userId"));
		if(!$user instanceof $userModel) {
			$this->error("User with id specified not found.");
			return;
		}

		if($this->option("detach-all")) {
			$user->detachAllPermissions();
			$this->info("All roles detached from user '".$user->name."' succesfully");
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
			$user->detachPermission($permission);

			$this->info("Permission '".$permission->name."' detached from user '".$user->name."' succesfully");
		} else {
			$user->attachPermission($permission);

			$this->info("Permission '".$permission->name."' attacched to user '".$user->name."' succesfully");
		}
	}
}
