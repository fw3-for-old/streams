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

/**
 * テスト実施インターフェース
 */
interface TestInterface
{
    /**
     * 現在までに保存されたログを返します。
     *
     * @return  array   現在までに保存されたログ
     */
    public function getLogs();

    /**
     * セットアップ
     */
    public function setup();

    /**
     * * テスト一括実行器
     */
    public function __invoke();

    /**
     * ティアダウン
     */
    public function teardown();
}
