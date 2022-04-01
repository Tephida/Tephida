<style type="text/css">
    #audioMP{display: none !important;} .padcont {
        padding: 0 0 15px;
    }
</style>
<div class="al_audios_wrap fl_l">
    <div class="al_audio_player shadow_box">
        <table cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td class="al_audio_tb_col1">
                    <div class="bigPlay_but" id="pl_play">
                        <div class="icon icon-play-1"></div>
                    </div>
                    <div class="al_audio_nav prev icon-fast-bw" id="pl_prev"></div>
                    <div class="al_audio_nav next icon-fast-fw" id="pl_next"></div>
                </td>
                <td class="al_audio_tb_col2">
                    <div class="al_audio_info_wrap">
                        <div class="al_audio_names_wrap">
                            <div class="al_audio_time fl_r" id="pl_time">0:00</div>
                            <div class="audio_names" id="pl_names"><b>Artist</b> - title of the song</div>
                            <div class="clear"></div>
                        </div>
                        <div class="al_audio_progress_wrap" id="pl_progress_bl">
                            <div class="bg_line"></div>
                            <div class="load_line" id="pl_load_line"></div>
                            <div class="play_line" id="pl_play_line">
                                <div class="slider" id="pl_slider"></div>
                            </div>
                            <div class="audioTimesAP" id="pl_time_bl">
                                <div class="audioTAP_strlka">3:00</div>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="al_audio_tb_col3">
                    <div class="al_audio_info_wrap volume">
                        <div class="al_audio_progress_wrap" id="pl_volume">
                            <div class="bg_line"></div>
                            <div class="play_line" id="pl_volume_line">
                                <div class="slider"></div>
                            </div>
                            <div class="audioTimesAP">
                                <div class="audioTAP_strlka">3:00</div>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="al_audio_tb_col4">
                    <table class="al_audio_tools">
                        <tbody>
                        <tr>
                            <td>
                                <div class="icon-plus-2" id="pl_add"
                                     onmouseover="showTooltip(this, {text: 'Добавить в мой список', shift:[-1,7,0]});"></div>
                            </td>
                            <td>
                                <div class="icon-loop-1" id="pl_loop"
                                     onmouseover="showTooltip(this, {text: 'Повторять эту композицию', shift:[-1,7,0]});"></div>
                            </td>
                            <td>
                                <div class="icon-shuffle-2" id="pl_shuffle"
                                     onmouseover="showTooltip(this, {text: 'Случайный порядок', shift:[-1,7,0]});"></div>
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div id="atitle">{title}</div>
    <div class="al_audio_result_wrap shadow_box">
        [is_user]
        <div class="audio_search_bl">
            <input type="text" placeholder="Поиск" onkeyup="audio.search(this.value[public], {uid}[/public]);"
                   id="search_audio_val"/>
            <img src="/images/loading_mini.gif" id="search_preloader" class="no_display"/>
        </div>
        [/is_user]
        <div id="audios_res">
            {audios_res}
        </div>
        <div id="load_but"></div>
    </div>
</div>


<div class="al_audios_panel fl_l bsbb">
    <div class="big_title" dir="auto">Показывать музыку:</div>
    <div class="menu_item {my_music-active}" id="my_music"
         onclick="if(event.target.id !== 'upload_btn') audio.change_tab('my_music');">
        Мои аудиозаписи
        <div class="al_audio_add_but icon-plus-1 bsbb"
             onmouseover="showTooltip(this, {text: 'Добавить аудиозапись', shift:[2,5,0]});" id="upload_btn"
             onclick="audio.uploadBox()">+
        </div>
    </div>
    <div class="menu_item {public_audios-active}" {public_audios}>
        Аудио сообщества
        [owner]
        <div class="al_audio_add_but icon-plus-1 bsbb"
             onmouseover="showTooltip(this, {text: 'Добавить аудиозапись', shift:[2,5,0]});" id="upload_btn"
             onclick="audio.uploadBox('{uid}'); return false;">+
        </div>
        [/owner]
    </div>
    <!--<div class="menu_item {feed-active}" id="feed" onclick="audio.change_tab('feed');">Обновления друзей</div>
<div class="menu_item {recommendations-active}" id="recommendations" onclick="audio.change_tab('recommendations');">Рекомендации</div>-->
    <div class="menu_item {popular-active}" id="popular" onclick="audio.change_tab('popular');">Популярное</div>
    <div class="menu_item no_display" id="search_tab2">Результаты поиска</div>

    [friends_block]
    <div id="mainSearchFrBl">
        <div class="audioFriendsBlock" id="friendBlockMain"></div>
    </div>
    [/friends_block]

</div>

<div class="clear"></div>


<script type="text/javascript">
    $(document).ready(function () {
        audio.tabType = '{plname}';
        audio.init({init});
        audio.user_id = '{uid}';
        audio.a_user_fid = '';
        audio.uname = '{user_name}';
        audio.loadAll({uid}, 0);
        [friends_block]
        audio.LoadFriends();
        [/friends_block]
    });
</script>