<?php
/**
 * @copyright 2017 interactivesolutions
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Contact InteractiveSolutions:
 * E-mail: hello@interactivesolutions.lt
 * http://www.interactivesolutions.lt
 */

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombNewCore\Console;

use Cache;
use Carbon\Carbon;

/**
 * Class HCAdminMenu
 * @package InteractiveSolutions\HoneycombNewCore\Console
 */
class HCAdminMenu extends HCCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hc:admin-menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Go through honeycomb related packages and get all menu items';

    /**
     * Menu list holder
     *
     * @var array
     */
    private $adminMenuHolder = [];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->comment('Scanning menu items..');
        $this->generateMenu();
        $this->comment('-');
    }

    /**
     * Get admin menu
     */
    private function generateMenu(): void
    {
        $files = $this->getConfigFiles();

        if (!empty($files)) {
            foreach ($files as $file) {
                $fileContent = validateJSONFromPath($file);

                if (isset($fileContent['adminMenu'])) {
                    $this->adminMenuHolder = array_merge($this->adminMenuHolder, $fileContent['adminMenu']);
                }
            }
        }

        Cache::forget('hc-admin-menu');
        Cache::put('hc-admin-menu', $this->adminMenuHolder, Carbon::now()->addWeek());
    }
}
