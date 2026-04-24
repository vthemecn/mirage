<?php
/**
 * Copyright (c) vtheme.cn
 *
 * @author vthemecn <mail@vtheme.cn>
 * @link https://vtheme.cn
 */

namespace vtheme\common;

class UserStat {

    /**
     * 获取用户发布的文章数量
     * 
     * @param int $userId 用户ID
     * @param bool $includePrivate 是否包含未公开的文章，默认false只统计公开文章
     * @return int 文章数量
     */
    public static function getUserPostsCount($userId, $includePrivate = false) {
        if (empty($userId)) {
            return 0;
        }
        
        if ($includePrivate) {
            // 包含所有状态的文章
            global $wpdb;
            $sql = "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_author = %d AND post_type = 'post'";
            $count = $wpdb->get_var($wpdb->prepare($sql, $userId));
        } else {
            // 只统计公开的文章（默认行为）
            $count = count_user_posts($userId, 'post', true);
        }
        
        return $count ? (int)$count : 0;
    }

    /**
     * 获取用户的收藏数量
     * 
     * @param int $userId 用户ID
     * @param bool $includePrivate 是否包含未公开文章的收藏，默认false只统计公开文章收藏
     * @return int 收藏数量
     */
    public static function getUserStarsCount($userId, $includePrivate = false) {
        if (empty($userId)) {
            return 0;
        }
        
        global $wpdb;
        $tableName = $wpdb->prefix . 'vt_star';
        
        if ($includePrivate) {
            // 包含所有文章的收藏（无论文章是否公开）
            $sql = "SELECT COUNT(DISTINCT s.id) FROM {$tableName} s 
                    LEFT JOIN {$wpdb->posts} p ON s.object_id = p.ID 
                    WHERE s.user_id = %d AND s.type = 'star'";
        } else {
            // 只统计公开文章的收藏（默认行为）
            $sql = "SELECT COUNT(DISTINCT s.id) FROM {$tableName} s 
                    LEFT JOIN {$wpdb->posts} p ON s.object_id = p.ID 
                    WHERE s.user_id = %d AND s.type = 'star' AND p.post_status = 'publish'";
        }
        
        $count = $wpdb->get_var($wpdb->prepare($sql, $userId));
        return $count ? (int)$count : 0;
    }

    /**
     * 获取用户的点赞数量
     * 
     * @param int $userId 用户ID
     * @param bool $includePrivate 是否包含未公开文章的点赞，默认false只统计公开文章点赞
     * @return int 点赞数量
     */
    public static function getUserLikesCount($userId, $includePrivate = false) {
        if (empty($userId)) {
            return 0;
        }
        
        global $wpdb;
        $tableName = $wpdb->prefix . 'vt_star';
        
        if ($includePrivate) {
            // 包含所有文章的点赞（无论文章是否公开）
            $sql = "SELECT COUNT(DISTINCT s.id) FROM {$tableName} s 
                    LEFT JOIN {$wpdb->posts} p ON s.object_id = p.ID 
                    WHERE s.user_id = %d AND s.type = 'like'";
        } else {
            // 只统计公开文章的点赞（默认行为）
            $sql = "SELECT COUNT(DISTINCT s.id) FROM {$tableName} s 
                    LEFT JOIN {$wpdb->posts} p ON s.object_id = p.ID 
                    WHERE s.user_id = %d AND s.type = 'like' AND p.post_status = 'publish'";
        }
        
        $count = $wpdb->get_var($wpdb->prepare($sql, $userId));
        return $count ? (int)$count : 0;
    }

    /**
     * 获取用户的评论数量
     * 
     * @param int $userId 用户ID
     * @param bool $includeUnapproved 是否包含未审核的评论，默认false只统计已审核评论
     * @return int 评论数量
     */
    public static function getUserCommentsCount($userId, $includeUnapproved = false) {
        if (empty($userId)) {
            return 0;
        }
        
        // 检查是否有缓存的评论数
        $cacheKey = $includeUnapproved ? 'comment_count_all' : 'comment_count';
        $count = get_user_meta($userId, $cacheKey, true);
        
        if ($count === '' || $count === false) {
            // 如果没有缓存，计算并缓存
            global $wpdb;
            
            if ($includeUnapproved) {
                // 包含所有评论（包括未审核的）
                $sql = "SELECT COUNT(*) FROM {$wpdb->comments} WHERE user_id = %d";
            } else {
                // 只统计已审核的评论（默认行为）
                $sql = "SELECT COUNT(*) FROM {$wpdb->comments} WHERE user_id = %d AND comment_approved = '1'";
            }
            
            $count = $wpdb->get_var($wpdb->prepare($sql, $userId));
            $count = $count ? (int)$count : 0;
            update_user_meta($userId, $cacheKey, $count);
        }
        
        return (int)$count;
    }

    /**
     * 清除用户的统计缓存
     * 
     * @param int $userId 用户ID
     * @return void
     */
    public static function clearUserStatsCache($userId) {
        if (empty($userId)) {
            return;
        }
        
        delete_user_meta($userId, 'comment_count');
        delete_user_meta($userId, 'comment_count_all');
    }

}