<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("CREATE VIEW `DuesMemberView` AS select `kawandesa`.`duesPaymentLog`.`id` AS `id`,`kawandesa`.`duesPaymentLog`.`dues_member` AS `dues_member`,`kawandesa`.`duesPaymentLog`.`amount_paid` AS `amount_paid`,`kawandesa`.`duesPaymentLog`.`paid_for` AS `paid_for`,`kawandesa`.`duesPaymentLog`.`created_at` AS `created_at`,`kawandesa`.`duesPaymentLog`.`created_by` AS `created_by`,`kawandesa`.`duesPaymentLog`.`updated_at` AS `updated_at`,`kawandesa`.`duesPaymentLog`.`updated_by` AS `updated_by`,`kawandesa`.`duesPaymentLog`.`deleted_at` AS `deleted_at`,`kawandesa`.`duesPaymentLog`.`deleted_by` AS `deleted_by` from `kawandesa`.`duesPaymentLog` where `kawandesa`.`duesPaymentLog`.`deleted_at` is null and exists(select 1 from `kawandesa`.`dues_member` where `kawandesa`.`duesPaymentLog`.`dues_member` = `kawandesa`.`dues_member`.`id` and exists(select 1 from `kawandesa`.`dues` where `kawandesa`.`dues_member`.`dues` = `kawandesa`.`dues`.`id` and `kawandesa`.`dues`.`id` = '1' and `kawandesa`.`dues`.`deleted_at` is null limit 1) and `kawandesa`.`dues_member`.`deleted_at` is null limit 1) and exists(select 1 from `kawandesa`.`dues_member` where `kawandesa`.`duesPaymentLog`.`dues_member` = `kawandesa`.`dues_member`.`id` and exists(select 1 from `kawandesa`.`civilian` where `kawandesa`.`dues_member`.`member` = `kawandesa`.`civilian`.`id` and `kawandesa`.`civilian`.`id` = '1' and `kawandesa`.`civilian`.`deleted_at` is null limit 1) and `kawandesa`.`dues_member`.`deleted_at` is null limit 1) group by `kawandesa`.`duesPaymentLog`.`id`,month(from_unixtime(`kawandesa`.`duesPaymentLog`.`paid_for`)),year(from_unixtime(`kawandesa`.`duesPaymentLog`.`paid_for`)) limit 0,10");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS `DuesMemberView`");
    }
};
