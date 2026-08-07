<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('streamers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('code', 20)->unique();
            $table->string('channel_name')->nullable();
            $table->string('platform')->nullable();
            $table->string('channel_url')->nullable();
            $table->string('platform_channel_id')->nullable();
            $table->text('avatar_url')->nullable();
            $table->boolean('is_live')->default(false);
            $table->timestamp('platform_data_updated_at')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(2.00);
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('total_earnings', 15, 2)->default(0);
            $table->decimal('total_earned', 12, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('streamer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('order_amount', 12, 2);
            $table->decimal('commission_amount', 12, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->string('status', 50)->default('credited');
            $table->timestamps();
        });

        Schema::create('streamer_withdraws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // Add streamer columns to orders table
        if (!Schema::hasColumn('orders', 'streamer_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('streamer_code')->nullable();
            });
        }
        
        if (!Schema::hasColumn('orders', 'streamer_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('streamer_id')->nullable();
                $table->foreign('streamer_id')->references('id')->on('streamers')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'streamer_id')) {
                $table->dropForeign(['streamer_id']);
                $table->dropColumn('streamer_id');
            }
            if (Schema::hasColumn('orders', 'streamer_code')) {
                $table->dropColumn('streamer_code');
            }
        });

        Schema::dropIfExists('streamer_withdraws');
        Schema::dropIfExists('streamer_histories');
        Schema::dropIfExists('streamers');
    }
};