<?php

namespace src\database\seeds_dao;

// ダミーデータの数を管理するクラス
class SeedCount
{
    const USERS = 2000;
    const INFLUENCERS = 50; // user_id 1 ~ 50 をインフルエンサーとする
    const POSTS = 6000; //1ユーザーあたり3つの投稿
    const POST_LIKES = 36000; 
    const COMMENTS = 18000;
    const CHILD_COMMENTS = 6000;
    const COMMENT_LIKES = 3000;
    const MIN_FOLLOW = 10;
    const MAX_FOLLOW = 100;
    const POSTS_FOR_PROTO = 3;
    const POST_LIKES_FOR_PROTO = 5;
    const POST_LIKES_FOR_INFLUENCER = 20;
    const COMMENTS_FOR_PROTO = 1;

}
