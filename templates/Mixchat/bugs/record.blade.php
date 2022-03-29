@foreach($bugs as $row)
    <div class="card mb-2">
        <div class="card-body">
            <div class="bug_item">
                <a href="" onclick="bugs.view({id}); return false;"><img src="{{ $row['ava'] }}"
                                                                         alt="{{ $row['title'] }}"></a>
                <div class="cont">
                    <div class="title"><a href="/bugs/{{ $row['id'] }}" onclick="Page.Go(this.href); return false;"
                                {{--onclick="bugs.view({{ $row['id'] }}); return false;"--}}>{{ $row['title'] }}</a>
                    </div>
                    <div class="author">{{ $row['sex'] }} <a href="/u{{ $row['uid'] }}"
                                                             onclick="Page.Go(this.href); return false;">{{ $row['name'] }}</a>
                    </div>
                </div>
                <div class="status_bug">
                    <div class="state">Статус:{{ $row['status'] }}</div>
                    <div class="adddate">обновлено {{ $row['date'] }}</div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
@endforeach