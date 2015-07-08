<?php namespace Bican\Roles\Console;

use Bican\Roles\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class AddroleCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'roles:addrole {name} {slug?} {level=1} {description?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Add a role to the database';

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
			'level' => (int)$this->argument("level") > 0 ? (int)$this->argument("level") : 1,
		];

		if($this->argument("description") != "") {
			$data['description'] = $this->argument("description");
		}

		$roleModel = config('roles.models.role');
		if($roleModel::where("name",$data['name'])->orWhere("slug",$data['slug'])->count() > 0) {
			$this->error("A role with the same name or slug already exists");
			return;
		}

		try {
			$newModel = $roleModel::create($data);
		} catch (QueryException $e) {
			$this->error("Failed to create role group: \n".$e->getMessage());
			return;
		}

		if(!$newModel instanceof Role) {
			$this->error("Failed to create role group");
		}

		$this->info("Role ".$data['name']." created succesfully");
	}
}
