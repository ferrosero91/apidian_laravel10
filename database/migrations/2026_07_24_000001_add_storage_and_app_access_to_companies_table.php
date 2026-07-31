<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'storage_mode')) {
                $table->string('storage_mode', 10)->nullable()->default(null)->after('phone');
            }
            if (!Schema::hasColumn('companies', 'aws_access_key_id')) {
                $table->string('aws_access_key_id')->nullable()->after('storage_mode');
            }
            if (!Schema::hasColumn('companies', 'aws_secret_access_key')) {
                $table->string('aws_secret_access_key')->nullable()->after('aws_access_key_id');
            }
            if (!Schema::hasColumn('companies', 'aws_default_region')) {
                $table->string('aws_default_region')->nullable()->default('us-east-1')->after('aws_secret_access_key');
            }
            if (!Schema::hasColumn('companies', 'aws_bucket')) {
                $table->string('aws_bucket')->nullable()->after('aws_default_region');
            }
            if (!Schema::hasColumn('companies', 'aws_url')) {
                $table->string('aws_url')->nullable()->after('aws_bucket');
            }
            if (!Schema::hasColumn('companies', 'app_access_enabled')) {
                $table->boolean('app_access_enabled')->default(false)->after('aws_url');
            }
            if (!Schema::hasColumn('companies', 'app_device_limit')) {
                $table->integer('app_device_limit')->default(1)->after('app_access_enabled');
            }
            if (!Schema::hasColumn('companies', 'app_access_token')) {
                $table->string('app_access_token')->nullable()->after('app_device_limit');
            }
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'storage_mode',
                'aws_access_key_id',
                'aws_secret_access_key',
                'aws_default_region',
                'aws_bucket',
                'aws_url',
                'app_access_enabled',
                'app_device_limit',
                'app_access_token',
            ]);
        });
    }
};
