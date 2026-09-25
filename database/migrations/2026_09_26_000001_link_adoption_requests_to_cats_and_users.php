<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Requests now point at the cat and the applicant's account. Existing rows keep NULL keys.
     * Status values are unified (Pending, Approved, Rejected, Released) and the two
     * decision dates become real timestamps.
     */
    public function up(): void
    {
        Schema::table('adoption_request', function (Blueprint $table) {
            $table->foreignId('cat_id')->nullable()->constrained('cats')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('color')->nullable()->change();
            $table->index('status');
        });

        $statuses = [
            'Approved' => ['approved'],
            'Rejected' => ['not approved', 'rejected'],
            'Released' => ['released'],
        ];
        foreach ($statuses as $status => $legacy) {
            DB::table('adoption_request')->whereIn(DB::raw('LOWER(TRIM(status))'), $legacy)->update(['status' => $status]);
        }
        DB::table('adoption_request')
            ->where(fn ($query) => $query->whereNull('status')->orWhereNotIn('status', array_keys($statuses)))
            ->update(['status' => 'Pending']);

        // Anything that isn't a readable date can't be converted, so it is cleared rather than failing the migration.
        foreach (['approval_date', 'Release_date'] as $column) {
            DB::table('adoption_request')->whereNotNull($column)->orderBy('id')->each(function ($row) use ($column) {
                $value = strtotime((string) $row->{$column});
                DB::table('adoption_request')->where('id', $row->id)
                    ->update([$column => $value === false ? null : date('Y-m-d H:i:s', $value)]);
            });
        }

        Schema::table('adoption_request', function (Blueprint $table) {
            $table->string('status')->default('Pending')->change();
        });

        if (DB::getDriverName() === 'pgsql') {
            // Postgres won't cast text to timestamp implicitly; the values were normalised above.
            foreach (['approval_date', 'Release_date'] as $column) {
                DB::statement("ALTER TABLE adoption_request ALTER COLUMN \"{$column}\" TYPE timestamp(0) without time zone USING \"{$column}\"::timestamp(0) without time zone");
            }
        } else {
            Schema::table('adoption_request', function (Blueprint $table) {
                $table->timestamp('approval_date')->nullable()->change();
                $table->timestamp('Release_date')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('adoption_request', function (Blueprint $table) {
            $table->string('approval_date')->nullable()->change();
            $table->string('Release_date')->nullable()->change();
            $table->string('status')->default('pending')->change();
            $table->dropIndex(['status']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('cat_id');
        });

        DB::table('adoption_request')->where('status', 'Rejected')->update(['status' => 'Not approved']);
    }
};
