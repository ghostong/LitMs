<?php
/**
 * 测试模块
 */

class WelcomeModel extends Lit\Ms\LitMsModel {

    public function welcome () {

        return Success(["Welcome LitMs"]);

    }

}