<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BuildDocbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docbox {--remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command builds docbox and serve it as a static page';

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
     * @return int
     */
    public function handle()
    {
        if ($this->option('remove')) {
            $this->remove();
        } else {
            $this->build();
        }
    }
    private function assets()
    {
        $destinationCss = public_path('css');
        $docboxPath = base_path('docbox');

        return [
            [$docboxPath . '/css/base.css', $destinationCss . '/base.css'],
            [$docboxPath . '/css/style.css', $destinationCss . '/style.css'],
            [$docboxPath . '/css/railscasts.css', $destinationCss . '/railscasts.css'],
            [$docboxPath . '/css/icon.eot', $destinationCss . '/icon.eot'],
            [$docboxPath . '/css/icon.woff', $destinationCss . '/icon.woff'],
            [$docboxPath . '/bundle.js', public_path('bundle.js')]
        ];
    }

    private function build()
    {
        $docboxPath = base_path('docbox');
        if (!is_dir($docboxPath)) {
            $this->error('DocBox path not found');
            return 0;
        }

        $this->info('Found `' . $docboxPath . '`. Building...');

        exec("cd {$docboxPath} && npm run build", $outputs);

        $this->info('Done building!');
        $this->info('Output:');
        foreach ($outputs as $output) {
            $this->line($output);
        }
        $this->newLine(2);

        $this->info('Copying HTML output...');
        $html = file_get_contents($docboxPath . '/index.html');
        $destinationHTML = public_path('docbox.html');

        file_put_contents(
            $destinationHTML,
            $html
        );
        $this->info('HTML copied to: ' . $destinationHTML);

        $destinationCss = public_path('css');

        if (!is_dir($destinationCss)) {
            $this->info('Creating CSS folder');
            mkdir($destinationCss);
            $this->info('Created css folder: ' . $destinationCss);
        }

        $this->info('Publishing asset files:');

        $assets = $this->assets();
        foreach ($assets as $asset) {
            $this->line($asset[0] . ' -> ' . $asset[1]);
            call_user_func_array('copy', $asset);
        }

        $this->comment('[ DONE ]');

        return 0;
    }

    private function remove()
    {
        $assets = $this->assets();

        $this->info('Deleting docbox files:');
        foreach ($assets as $asset) {
            if (file_exists($asset[1])) {
                $this->line('- '. $asset[1]);
                unlink($asset[1]);
            }
        }
        $html = public_path('docbox.html');
        if (file_exists($html)) {
            $this->info('Deleting docbox.html');
            unlink($html);
            $this->info('DELETED');
        }

        $this->comment('[ DONE ]');
    }
}
