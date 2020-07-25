<?php
/**  ___ _  _ ___  __         ___
 *  | _ | || | _ \/ _|___ _ _| ____ _____ _ _ _  _ ___ _ _  ___
 *  |  _| __ |  _|  _/ _ | '_| _|\ V / -_| '_| || / _ | ' \/ -_)
 *  |_| |_||_|_| |_| \___|_| |___|\_/\___|_|  \_, \___|_||_\___|
 *                                            |__/
 *
* Flywheel3: the inertia php framework for old php versions
 *
 * @category    test
 * @package     sample
 * @author      akira wakaba <wakabadou@gmail.com>
 * @copyright   Copyright (c) @2019  Wakabadou (http://www.wakabadou.net/) / Project ICKX (https://ickx.jp/). All rights reserved.
 * @license     http://opensource.org/licenses/MIT The MIT License.
 *              This software is released under the MIT License.
 * @varsion     1.0.0
 */

namespace fw3_for_old\tests\cases\filters\utilitys;

use fw3_for_old\streams\filters\utilitys\StreamFilterSpec;
use fw3_for_old\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3_for_old\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;
use fw3_for_old\tests\streams\tester\AbstractTest;

/**
 * ストリームフィルタスペックのテスト
 */
class StreamFilterSpecTest extends AbstractTest
{
    /**
     * Specインスタンスのテスト
     */
    public function testSpec()
    {
        $streamFilterSpec                   = StreamFilterSpec::factory();
        $streamFilterConvertEncodingSpec    = StreamFilterConvertEncodingSpec::factory();
        $streamFilterConvertLinefeedSpec    = StreamFilterConvertLinefeedSpec::factory();

        $this->assertEquals($streamFilterSpec->with(), $streamFilterSpec);
        $this->assertNotSame($streamFilterSpec->with(), $streamFilterSpec);

        $this->assertSame($streamFilterSpec->with()->appendWriteChain($streamFilterConvertEncodingSpec->with()->setupForSjisOut())->build(), 'php://filter/write=convert.encoding.SJIS-win:default');
        $this->assertSame($streamFilterSpec->with()->appendWriteChain($streamFilterConvertEncodingSpec->with()->setupForEucjpOut())->build(), 'php://filter/write=convert.encoding.eucJP-win:default');
        $this->assertSame($streamFilterSpec->with()->appendWriteChain($streamFilterConvertEncodingSpec->with()->setupForUtf8Out())->build(), 'php://filter/write=convert.encoding.UTF-8:default');

        $this->assertSame($streamFilterSpec->with()->appendWriteChain($streamFilterConvertLinefeedSpec->with()->setupForUnix())->build(), 'php://filter/write=convert.linefeed.LF:ALL');
        $this->assertSame($streamFilterSpec->with()->appendWriteChain($streamFilterConvertLinefeedSpec->with()->setupForWindows())->build(), 'php://filter/write=convert.linefeed.CRLF:ALL');
    }
}
