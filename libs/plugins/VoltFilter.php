<?php
namespace PFrame\Libs\Plugins;
use PFrame\Libs\Models\User;

/**
 * 添加自定义过滤器
 * @author Dqcenter
 *
 */
class VoltFilter {
    
    public function addFilters($compiler) {
        return true;
    }
}