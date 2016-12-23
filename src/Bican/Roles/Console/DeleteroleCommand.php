<?php namespace Bican\Roles\Console;

use Illuminate\Console\Command;
use Bican\Roles\Models\Role;

class DeleteroleCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'roles:deleterole {--id=} {--slug=}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Delete a role from the database';

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
		if((int)$this->option("id") > 0) {
			$model = $roleModel::find((int)$this->option("id"));
		} elseif($this->option("slug") != "") {
			$model = $roleModel::where("slug",$this->option("slug"))->first();
		} else {
			$this->error("No id or slug option specified. Please use --id= or --slug=");
			return;
		}

		if(!$model instanceof Role) {
			$this->error("Role with id or slug specified not found.");
			return;
		}

		$name = $model->name;

		if(!$model->delete()) {
			$this->error("Failed to delete the role group");
			return;
		}

		$this->info("Role '".$name."'' deleted succesfully");
	}
}
