<?php 
namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExtractBootstrapIcons extends Command
{
    protected $signature = 'icons:extract';
    protected $description = 'Extract Bootstrap icon class names from CSS';

    public function handle()
    {
        $css = file_get_contents('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css');

        preg_match_all('/\.bi-([a-z0-9-]+)::before/', $css, $matches);

        $icons = $matches[1] ?? [];

        // Simpan ke file helper (array PHP)
        $output = "<?php\n\nreturn " . var_export($icons, true) . ";\n";
        file_put_contents(base_path('bootstrap-icons.php'), $output);

        $this->info("Found " . count($icons) . " icons.");
    }
}
