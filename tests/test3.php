<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 zhgzhg
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
 */

// Simple Demonstration how use GPhpThread

require_once __DIR__ . '/../GPhpThread.php';

class MyThread extends GPhpThread {
	public function run() {
		echo 'Hello, I am a thread with id ' . $this->getPid() . "!\nTrying to lock the critical section\n";

		$lock = new GPhpThreadLockGuard($this->criticalSection);

		echo "=--- locked " . $this->getPid() . "\n";
		$this->criticalSection->addOrUpdateResource('IAM', $this->getPid());
		$this->criticalSection->addOrUpdateResource('IAMNOT', '0xdead1');
		$this->criticalSection->removeResource('IAMNOT');
		echo "=--- unlocked " . $this->getPid() . "\n";
	}
}

echo "Master main EP " . getmypid() . "\n";

$criticalSection = new GPhpThreadCriticalSection();
$criticalSection->cleanPipeGarbage(); // remove any garbage left from any ungracefully terminated previous executions

echo "\nLaunching Thread1...\n\n";

$thr1 = new MyThread($criticalSection, true);
$thr2 = new MyThread($criticalSection, true);
$thr1->start();
echo "Thread1 pid is: " . $thr1->getPid() . "\n";
$thr2->start();

$thr2->join();
$thr1->join();
?>
