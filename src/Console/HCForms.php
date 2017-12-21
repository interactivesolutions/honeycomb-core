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

namespace InteractiveSolutions\HoneycombCore\Console;

use Cache;
use Carbon\Carbon;
use InteractiveSolutions\HoneycombCore\Console\HCCommand;

/**
 * Class HCForms
 * @package InteractiveSolutions\HoneycombCore\Console
 */
class HCForms extends HCCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hc:forms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Go through honeycomb related packages and get all form items';


    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->comment('Scanning form items..');
        $this->generateFormData();
        $this->comment('-');
    }

    /**
     * Generating form data
     */
    private function generateFormData(): void
    {
        $files = $this->getConfigFiles();
        $formDataHolder = [];


        if (!empty($files)) {
            foreach ($files as $file) {

                $file = json_decode(file_get_contents($file), true);

                if (isset($file['formData'])) {
                    $formDataHolder = array_merge($formDataHolder, $file['formData']);
                }
            }
        }

        Cache::forget('hc-forms');
        Cache::put('hc-forms', $formDataHolder, Carbon::now()->addMonth());
    }
}
