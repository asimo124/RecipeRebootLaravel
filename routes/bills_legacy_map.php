<?php

/**
 * Maps legacy BillsSite API paths (relative to /api/) to Laravel controller actions.
 *
 * @return array<string, array{0: class-string, 1: string}>
 */
return [
    // auth
    'auth/login.php' => ['App\Http\Controllers\Api\Bills\AuthController', 'login'],
    'auth/logout.php' => ['App\Http\Controllers\Api\Bills\AuthController', 'logout'],
    'auth/me.php' => ['App\Http\Controllers\Api\Bills\AuthController', 'me'],

    // apple_notes
    'apple_notes/export.php' => ['App\Http\Controllers\Api\Bills\AppleNotesController', 'export'],
    'apple_notes/upload.php' => ['App\Http\Controllers\Api\Bills\AppleNotesController', 'upload'],
    'deleteAppleNotes.php' => ['App\Http\Controllers\Api\Bills\AppleNotesController', 'destroy'],
    'loadAppleNotes.php' => ['App\Http\Controllers\Api\Bills\AppleNotesController', 'index'],

    // audit
    'audit/upload.php' => ['App\Http\Controllers\Api\Bills\AuditController', 'upload'],

    // bills_admin
    'bills_admin/create.php' => ['App\Http\Controllers\Api\Bills\BillsAdminController', 'store'],
    'bills_admin/delete.php' => ['App\Http\Controllers\Api\Bills\BillsAdminController', 'destroy'],
    'bills_admin/get.php' => ['App\Http\Controllers\Api\Bills\BillsAdminController', 'show'],
    'bills_admin/list.php' => ['App\Http\Controllers\Api\Bills\BillsAdminController', 'index'],
    'bills_admin/update.php' => ['App\Http\Controllers\Api\Bills\BillsAdminController', 'update'],
    'bills_admin/update_audit.php' => ['App\Http\Controllers\Api\Bills\BillsAdminController', 'updateAudit'],
    'bills_admin/update_flags.php' => ['App\Http\Controllers\Api\Bills\BillsAdminController', 'updateFlags'],

    // bill dates
    'commitNewEndDates.php' => ['App\Http\Controllers\Api\Bills\BillDateController', 'commitEndDates'],
    'loadBillDates.php' => ['App\Http\Controllers\Api\Bills\BillDateController', 'index'],
    'loadBillDates2.php' => ['App\Http\Controllers\Api\Bills\BillDateController', 'indexAlt'],
    'updateBillDateEnabled.php' => ['App\Http\Controllers\Api\Bills\BillDateController', 'updateEnabled'],
    'updateBillDateMultiplier.php' => ['App\Http\Controllers\Api\Bills\BillDateController', 'updateMultiplier'],

    // date jobs
    'check_date_job_done.php' => ['App\Http\Controllers\Api\Bills\DateJobController', 'status'],
    'queue_date_job.php' => ['App\Http\Controllers\Api\Bills\DateJobController', 'queue'],

    // balance
    'getCurBalance.php' => ['App\Http\Controllers\Api\Bills\BalanceController', 'show'],
    'saveCurBalance.php' => ['App\Http\Controllers\Api\Bills\BalanceController', 'update'],

    // budget
    'loadBudgetDiscrepancies.php' => ['App\Http\Controllers\Api\Bills\BudgetController', 'discrepancies'],

    // charges
    'get_categories.php' => ['App\Http\Controllers\Api\Bills\ChargeController', 'categories'],
    'get_charge_detail.php' => ['App\Http\Controllers\Api\Bills\ChargeController', 'show'],
    'get_charges.php' => ['App\Http\Controllers\Api\Bills\ChargeController', 'index'],
    'get_charges_by_category.php' => ['App\Http\Controllers\Api\Bills\ChargeController', 'indexByCategory'],
    'get_final_charges.php' => ['App\Http\Controllers\Api\Bills\ChargeController', 'final'],
    'load_charges.php' => ['App\Http\Controllers\Api\Bills\ChargeController', 'load'],
    'load_charges2.php' => ['App\Http\Controllers\Api\Bills\ChargeController', 'loadAlt'],
    'save_charge_category.php' => ['App\Http\Controllers\Api\Bills\ChargeController', 'updateCategory'],

    // credit_utilization
    'credit_utilization/bills.php' => ['App\Http\Controllers\Api\Bills\CreditUtilizationController', 'bills'],
    'credit_utilization/create.php' => ['App\Http\Controllers\Api\Bills\CreditUtilizationController', 'store'],
    'credit_utilization/delete.php' => ['App\Http\Controllers\Api\Bills\CreditUtilizationController', 'destroy'],
    'credit_utilization/get.php' => ['App\Http\Controllers\Api\Bills\CreditUtilizationController', 'show'],
    'credit_utilization/list.php' => ['App\Http\Controllers\Api\Bills\CreditUtilizationController', 'index'],
    'credit_utilization/update.php' => ['App\Http\Controllers\Api\Bills\CreditUtilizationController', 'update'],

    // dates
    'get_dates.php' => ['App\Http\Controllers\Api\Bills\DateController', 'index'],

    // dietlog
    'dietlog/add_oatmeal.php' => ['App\Http\Controllers\Api\Bills\DietlogController', 'addOatmeal'],
    'dietlog_entry_create.php' => ['App\Http\Controllers\Api\Bills\DietlogEntryController', 'store'],
    'dietlog_entry_delete.php' => ['App\Http\Controllers\Api\Bills\DietlogEntryController', 'destroy'],
    'dietlog_entry_update.php' => ['App\Http\Controllers\Api\Bills\DietlogEntryController', 'update'],
    'dietlog_food_create.php' => ['App\Http\Controllers\Api\Bills\DietlogFoodController', 'store'],
    'dietlog_food_delete.php' => ['App\Http\Controllers\Api\Bills\DietlogFoodController', 'destroy'],
    'dietlog_food_update.php' => ['App\Http\Controllers\Api\Bills\DietlogFoodController', 'update'],
    'dietlog_foods.php' => ['App\Http\Controllers\Api\Bills\DietlogFoodController', 'index'],
    'dietlog_foods_public.php' => ['App\Http\Controllers\Api\Bills\DietlogFoodController', 'indexPublic'],
    'dietlog_inc.php' => ['App\Http\Controllers\Api\Bills\DietlogController', 'inc'],
    'dietlog_log.php' => ['App\Http\Controllers\Api\Bills\DietlogLogController', 'index'],
    'dietlog_log_public.php' => ['App\Http\Controllers\Api\Bills\DietlogLogController', 'indexPublic'],
    'dietlog_lookups.php' => ['App\Http\Controllers\Api\Bills\DietlogController', 'lookups'],
    'dietlog_suggested_meal.php' => ['App\Http\Controllers\Api\Bills\DietlogController', 'suggestedMeal'],

    // disposable
    'disposable/upload_import.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'uploadImport'],
    'disposable/upload_preview.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'uploadPreview'],
    'loadDisposableAccountNames.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'accountNames'],
    'loadDisposableAccountNumbers.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'accountNumbers'],
    'loadDisposableAccountTypes.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'accountTypes'],
    'loadDisposableCategoryNames.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'categoryNames'],
    'loadDisposableInstitutionNames.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'institutionNames'],
    'loadDisposableTransactions.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'transactions'],
    'loadDisposableTransactionsChartData.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'chartData'],
    'loadDisposableTransactionsChartDataCategory.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'chartDataCategory'],
    'loadDisposableTransactionsChartDataDay.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'chartDataDay'],
    'updateAllNotCovered.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'updateAllNotCovered'],
    'updateDisposableTransactionCovered.php' => ['App\Http\Controllers\Api\Bills\DisposableController', 'updateTransactionCovered'],

    // expenses
    'expenses/create.php' => ['App\Http\Controllers\Api\Bills\ExpenseController', 'store'],
    'expenses/delete.php' => ['App\Http\Controllers\Api\Bills\ExpenseController', 'destroy'],
    'expenses/list.php' => ['App\Http\Controllers\Api\Bills\ExpenseController', 'index'],
    'expenses/update.php' => ['App\Http\Controllers\Api\Bills\ExpenseController', 'update'],
    'loadExpensesAppData.php' => ['App\Http\Controllers\Api\Bills\ExpensesAppController', 'show'],
    'syncExpenses.php' => ['App\Http\Controllers\Api\Bills\ExpenseController', 'sync'],
    'updateExpensesAppCollapsed.php' => ['App\Http\Controllers\Api\Bills\ExpensesAppController', 'updateCollapsed'],

    // food log
    'addFoodLogGeneralItem.php' => ['App\Http\Controllers\Api\Bills\FoodLogController', 'storeGeneralItem'],
    'addFoodLogItem.php' => ['App\Http\Controllers\Api\Bills\FoodLogController', 'storeItem'],
    'editFoodLogGeneralItem.php' => ['App\Http\Controllers\Api\Bills\FoodLogController', 'updateGeneralItem'],
    'loadFoodLog.php' => ['App\Http\Controllers\Api\Bills\FoodLogController', 'index'],
    'loadFoodSensitivities.php' => ['App\Http\Controllers\Api\Bills\FoodLogController', 'sensitivities'],
    'loadFoodSensitivitiesGeneral.php' => ['App\Http\Controllers\Api\Bills\FoodLogController', 'sensitivitiesGeneral'],
    'removeFoodLogGeneralItem.php' => ['App\Http\Controllers\Api\Bills\FoodLogController', 'destroyGeneralItem'],
    'removeFoodLogItem.php' => ['App\Http\Controllers\Api\Bills\FoodLogController', 'destroyItem'],

    // google maps
    'request_google_maps_trip_duration.php' => ['App\Http\Controllers\Api\Bills\GoogleMapsController', 'tripDuration'],

    // pay periods & paychecks
    'getComingPayDates.php' => ['App\Http\Controllers\Api\Bills\PayPeriodController', 'comingPayDates'],
    'loadPayPeriodItems.php' => ['App\Http\Controllers\Api\Bills\PayPeriodController', 'items'],
    'loadPayPeriods.php' => ['App\Http\Controllers\Api\Bills\PayPeriodController', 'index'],
    'save_pay_period_num_days.php' => ['App\Http\Controllers\Api\Bills\PayPeriodController', 'updateNumDays'],
    'updatePaycheckDisposable.php' => ['App\Http\Controllers\Api\Bills\PaycheckController', 'updateDisposable'],

    // pill history
    'pill_history/get_history.php' => ['App\Http\Controllers\Api\Bills\PillHistoryController', 'index'],

    // push notifications
    'push_notifications/assign_schedule.php' => ['App\Http\Controllers\Api\Bills\PushNotificationController', 'assignSchedule'],
    'push_notifications/create.php' => ['App\Http\Controllers\Api\Bills\PushNotificationController', 'store'],
    'push_notifications/delete.php' => ['App\Http\Controllers\Api\Bills\PushNotificationController', 'destroy'],
    'push_notifications/get.php' => ['App\Http\Controllers\Api\Bills\PushNotificationController', 'show'],
    'push_notifications/inc.php' => ['App\Http\Controllers\Api\Bills\PushNotificationController', 'inc'],
    'push_notifications/list.php' => ['App\Http\Controllers\Api\Bills\PushNotificationController', 'index'],
    'push_notifications/schedule_create.php' => ['App\Http\Controllers\Api\Bills\PushNotificationController', 'scheduleStore'],
    'push_notifications/schedule_delete.php' => ['App\Http\Controllers\Api\Bills\PushNotificationController', 'scheduleDestroy'],
    'push_notifications/schedule_get.php' => ['App\Http\Controllers\Api\Bills\PushNotificationController', 'scheduleShow'],
    'push_notifications/schedule_update.php' => ['App\Http\Controllers\Api\Bills\PushNotificationController', 'scheduleUpdate'],
    'push_notifications/update.php' => ['App\Http\Controllers\Api\Bills\PushNotificationController', 'update'],

    // recipe react
    'recipe_react/get_recipes.php' => ['App\Http\Controllers\Api\Bills\RecipeReactController', 'index'],

    // rocket money
    'loadRocketMoneyData.php' => ['App\Http\Controllers\Api\Bills\RocketMoneyController', 'show'],
    'updateRocketMoneyCollapsed.php' => ['App\Http\Controllers\Api\Bills\RocketMoneyController', 'updateCollapsed'],

    // settings
    'settings/reset_test_db.php' => ['App\Http\Controllers\Api\Bills\SettingsController', 'resetTestDb'],
    'settings/test_mode.php' => ['App\Http\Controllers\Api\Bills\SettingsController', 'testMode'],

    // title matches
    'insertTitleMatch.php' => ['App\Http\Controllers\Api\Bills\TitleMatchController', 'store'],
    'loadTitleMatches.php' => ['App\Http\Controllers\Api\Bills\TitleMatchController', 'index'],
    'removeTitleMatch.php' => ['App\Http\Controllers\Api\Bills\TitleMatchController', 'destroy'],

    // track progress
    'calcTrackProgress.php' => ['App\Http\Controllers\Api\Bills\TrackProgressController', 'calculate'],

    // transactions
    'loadTransactionAll.php' => ['App\Http\Controllers\Api\Bills\TransactionController', 'index'],
    'loadTransactionCategories.php' => ['App\Http\Controllers\Api\Bills\TransactionController', 'categories'],
    'loadTransactionDrilldown.php' => ['App\Http\Controllers\Api\Bills\TransactionController', 'drilldown'],

    // upcoming purchases
    'addUpcomingPurchase.php' => ['App\Http\Controllers\Api\Bills\UpcomingPurchaseController', 'store'],
    'removeUpcomingPurchase.php' => ['App\Http\Controllers\Api\Bills\UpcomingPurchaseController', 'destroy'],
];
