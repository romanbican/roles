<?php namespace Bican\Roles\Console;

use Illuminate\Console\Command;
use Bican\Roles\Models\Role;

class AttachroletouserCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'roles:attachroletouser {--detach-all} {--detach} {userId} {roleIdOrSlug?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Attach or detach a role to/from a user';

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
		$roleModel = config('roles.models.role');
		$userModel = config('auth.model');

		$user = $userModel::find($this->argument("userId"));
		if(!$user instanceof $userModel) {
			$this->error("User with id specified not found.");
			return;
		}

		if($this->option("detach-all")) {
			$user->detachAllRoles();
			$this->info("All roles detached from user '".$user->name."' succesfully");
			return;
		}

		if((int)$this->argument("roleIdOrSlug") > 0 && (int)$this->argument("roleIdOrSlug") == $this->argument("roleIdOrSlug")) {
			$role = $roleModel::find((int)$this->argument("roleIdOrSlug"));
		} elseif($this->argument("roleIdOrSlug") != "") {
			$role = $roleModel::where("slug",$this->argument("roleIdOrSlug"))->first();
		} else {
			$this->error("Invalid or empty role id or slug");
			return;
		}

		if(!$role instanceof Role) {
			$this->error("Role with id or slug specified not found.");
			return;
		}

		if($this->option('detach')) {
			$user->detachRole($role);

			$this->info("Role '".$role->name."' detached from user '".$user->name."' succesfully");
		} else {
			$user->attachRole($role);

			$this->info("Role '".$role->name." attacched to user '".$user->name."' succesfully");
		}
	}
}
