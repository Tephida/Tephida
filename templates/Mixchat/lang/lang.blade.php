<div class="miniature_box">
    <div class="miniature_pos">
        @foreach($lang_list as $languages => $value)
            @if($languages === $user_lang)
                <div class="lang_but lang_selected">{{ $value['name'] }}</div>
            @else
                <a href="/langs/change?id={{ $languages }}" style="text-decoration:none"><div class="lang_but">{{ $value['name'] }}</div></a>
            @endif
        @endforeach
    </div>
</div>