{{--
    Renders one schema field as the appropriate input.
    Expects: $field (schema definition), $name (form field name, e.g.
    "content[hero_title]" or "content[offices][0][phone]"), $value (current value).
--}}

@php
    $type = $field['type'];
    $inputId = 'f_' . \Illuminate\Support\Str::slug($name, '_');
@endphp

<div class="sx-field">
    <label class="sx-field-label" for="{{ $inputId }}">{{ $field['label'] }}</label>

    @if($type === 'text')
        <input type="text" id="{{ $inputId }}" name="{{ $name }}" value="{{ $value }}" class="sx-input">

    @elseif($type === 'textarea')
        <textarea id="{{ $inputId }}" name="{{ $name }}" class="sx-input sx-textarea" rows="4">{{ $value }}</textarea>

    @elseif($type === 'richtext')
        <textarea id="{{ $inputId }}" name="{{ $name }}" class="sx-input sx-richtext" rows="8">{{ $value }}</textarea>

    @elseif($type === 'image')
        @php
            $previewSrc = $value;
            if ($value && !preg_match('#^(https?:)?//#', $value) && !empty($field['preview_base'])) {
                $previewSrc = asset(rtrim($field['preview_base'], '/') . '/' . ltrim($value, '/'));
            }
        @endphp
        <div class="sx-image-row">
            <input type="text" id="{{ $inputId }}" name="{{ $name }}" value="{{ $value }}" class="sx-input sx-image-input" placeholder="/public/uploads/... or full URL">
            <button type="button" class="sx-btn-browse">Browse</button>
            <input type="file" class="sx-image-file-input" accept="image/*" hidden>
        </div>
        <img src="{{ $previewSrc }}" class="sx-image-preview" style="{{ $value ? '' : 'display:none;' }}" onerror="this.style.display='none'" alt="">

    @elseif($type === 'repeater')
        <div class="sx-repeater" data-repeater>
            <div class="sx-repeater-items">
                @foreach(($value ?? []) as $i => $item)
                    <div class="sx-repeater-item">
                        <button type="button" class="sx-repeater-remove" title="Remove">&times;</button>
                        @foreach($field['fields'] as $sub)
                            @include('admin.pages.partials.field', [
                                'field' => $sub,
                                'name' => $name . '[' . $i . '][' . $sub['key'] . ']',
                                'value' => $item[$sub['key']] ?? null,
                            ])
                        @endforeach
                    </div>
                @endforeach
            </div>

            <template>
                <div class="sx-repeater-item">
                    <button type="button" class="sx-repeater-remove" title="Remove">&times;</button>
                    @foreach($field['fields'] as $sub)
                        @include('admin.pages.partials.field', [
                            'field' => $sub,
                            'name' => $name . '[__INDEX__][' . $sub['key'] . ']',
                            'value' => null,
                        ])
                    @endforeach
                </div>
            </template>

            <button type="button" class="sx-repeater-add">+ Add row</button>
        </div>
    @endif
</div>
