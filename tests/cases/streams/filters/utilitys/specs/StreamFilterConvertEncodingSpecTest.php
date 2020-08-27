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
use fw3_for_old\tests\streams\tester\AbstractTest;

/**
 * ストリームフィルタ：ConvertLinefeedSpecのテスト
 */
class StreamFilterConvertEncodingSpecTest extends AbstractTest
{
    /**
     * 現在のフィルタ名のストリームフィルタが登録されているかのテスト
     *
     * @runInSeparateProcess
     */
    public function testRegisteredFilterName()
    {
        StreamFilterSpec::registerConvertEncodingFilter();

        $this->assertTrue(StreamFilterConvertEncodingSpec::registeredFilterName());

        StreamFilterConvertEncodingSpec::filterName('test.convert.encoding');
        $this->assertFalse(StreamFilterConvertEncodingSpec::registeredFilterName());

        StreamFilterConvertEncodingSpec::filterName(StreamFilterConvertEncodingSpec::DEFAULT_FILTER_NAME);
    }
}
