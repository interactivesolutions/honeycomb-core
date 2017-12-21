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

use InteractiveSolutions\HoneycombCore\Console\HCCommand;


/**
 * Class HCAdminURL
 * @package InteractiveSolutions\HoneycombCore\Console
 */
class HCAdminURL extends HCCommand
{
    /**
     *
     */
    const KEY = 'HC_ADMIN_URL';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hc:admin-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate secure admin url';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->generateURL();
    }

    /**
     * Generate url
     */
    private function generateURL(): void
    {
        $url = 'admin' . random_str(8);

        addEnvVariable(self::KEY, $url);

        $this->info("Admin URL $url set successfully.");
    }
}
