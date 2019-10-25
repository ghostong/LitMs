<?php
/**
 * 测试模块
 */

class WelcomeModel extends Lit\LitMs\LitMsModel {

    public function welcome () {

        return Success(["Welcome LitMs"]);

    }

}