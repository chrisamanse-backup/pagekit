@if (posts)
<table class="uk-table uk-table-hover uk-table-middle">
    <thead>
        <tr>
            <th class="pk-table-width-minimum"><input type="checkbox" class="js-select-all"></th>
            <th class="pk-table-min-width-100">@trans('Title')</th>
            <th class="pk-table-width-100 uk-text-center">@trans('Status')</th>
            <th class="pk-table-width-200 pk-table-min-width-200">@trans('URL')</th>
            <th class="pk-table-width-200">@trans('Author')</th>
            <th class="pk-table-width-100 uk-text-center">@trans('Comments')</th>
            <th class="pk-table-width-100">@trans('Date')</th>
        </tr>
    </thead>
    <tbody>
        @foreach (posts as post)
        <tr>
            <td>
                <input type="checkbox" name="ids[]" class="js-select" value="@post.id">
            </td>
            <td>
                <a href="@url.route('@blog/post/edit', ['id' => post.id])">@post.title</a>
            </td>
            <td class="uk-text-center">
                <a href="#" data-action="@url.route('@blog/post/status', ['ids[]' => post.id, 'status' => post.status == 2 ? '3' : '2'])" title="@post.statusText">
                    <i class="uk-icon-circle uk-text-@(post.status == 2 ? 'success' : 'danger')" title="@post.statusText"></i>
                </a>
            </td>
            <td class="pk-table-text-break">
                @set(link = url.route('@blog/id', ['id' => post.id], 'base'))
                @if (post.status == 2 && post.hasAccess(app.user))
                <a href="@url.route('@blog/id', ['id' => post.id])" target="_blank">@link|urldecode</a>
                @else
                @(post.url ?: link)
                @endif
            </td>
            <td>
                <a href="@url.route('@system/user/edit', ['id' => post.user.id])">@post.user.username</a>
            </td>
            <td class="uk-text-center">
                <a href="@url.route('@blog/comment', ['post' => post.id])">@post.numcomments</a>
            </td>
            <td>
                @post.date|date('long')
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif