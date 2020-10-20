<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EverythingTable extends Migration
{
    public function up(): void
    {
        Schema::create('everythings', function (Blueprint $table) {
            // In sake of cheating.
            $table->increments('id');

            $table->char('char');
            $table->string('string');
            $table->text('text');
            $table->mediumText('mediumText');
            $table->longText('longText');

            $table->integer('integer');
            $table->tinyInteger('tinyInteger');
            $table->smallInteger('smallInteger');
            $table->mediumInteger('mediumInteger');
            $table->bigInteger('bigInteger');
            $table->unsignedInteger('unsignedInteger');
            $table->unsignedTinyInteger('unsignedTinyInteger');
            $table->unsignedSmallInteger('unsignedSmallInteger');
            $table->unsignedMediumInteger('unsignedMediumInteger');
            $table->unsignedBigInteger('unsignedBigInteger');

            $table->float('float');
            $table->double('double');
            // $table->decimal('decimal');
            $table->float('unsignedFloat')->unsigned();
            $table->double('unsignedDouble')->unsigned();
            // $table->unsignedDecimal('unsignedDecimal');

            $table->boolean('boolean');
            // json
            // jsonb
            // date
            // dateTime
            // dateTimeTz
            // time
            // timeTz
            // timestamp
            // timestampTz
            // year
            // binary
            // uuid
            // ipAddress
            // macAddress
            // geometry
            // point
            // lineString
            // polygon
            // geometryCollection
            // multiPoint
            // multiLineString
            // multiPolygon
            // multiPolygonZ
            // computed
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('everythings');
    }
}
