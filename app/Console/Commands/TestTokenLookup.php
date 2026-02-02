<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\MongoPersonalAccessToken;
use App\Models\User;

class TestTokenLookup extends Command
{
    protected $signature = 'test:token-lookup';
    protected $description = 'Test MongoDB token lookup functionality';

    public function handle()
    {
        $this->info('=== Testing MongoDB Token Lookup ===');

        try {
            // List all tokens
            $tokens = MongoPersonalAccessToken::all();
            $this->info("Total tokens in database: " . count($tokens));
            
            if (count($tokens) > 0) {
                foreach ($tokens as $token) {
                    $this->info("Token ID: " . $token->id . ", Name: " . $token->name);
                }
            }

            // Test finding the specific token we know about
            $this->info('\n4. Testing specific token lookup...');
            $testToken = 'KTwRsdG1SQn8lSr4Nytt4L7rafpkH6jM8f2nfgZu';
            $result = MongoPersonalAccessToken::findToken($testToken);
            $this->info('   ✓ findToken completed');
            $this->info('   ✓ Result: ' . ($result ? 'Found - ID: ' . $result->id : 'Not Found'));

            // Test the full authentication flow
            if ($result) {
                $this->info('\n5. Testing tokenable relationship...');
                try {
                    $user = $result->tokenable;
                    $this->info('   ✓ tokenable relationship: ' . ($user ? 'User ' . $user->email : 'NULL'));
                } catch (\Throwable $e) {
                    $this->error('   ✗ Error: ' . $e->getMessage());
                }
            }

            $this->info('\n=== Test Completed Successfully ===');
            return 0;
        } catch (\Throwable $e) {
            $this->error('ERROR: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
