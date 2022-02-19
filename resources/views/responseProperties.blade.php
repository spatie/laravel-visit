<div class="mx-4 my-1 ">
    <div class="m-1 w-full max-w-80 {{ $bgColor }}">
        <div class="w-full text-white text-center"></div>
        <div class="w-full text-white">
        <span class="px-2 text-left w-1/2">
            <span class="uppercase mr-1">{{ $method }}</span>
            <span>{{ $url }}</span>
        </span>
            <span class="px-2 text-right w-1/2">
            {{ $statusCode }}
        </span>
        </div>
        <div class="w-full text-white text-center"></div>
    </div>

    @if($showHeaders)
        <div class="underline mb-1">Headers</div>


            @foreach($headers as $name => $value)
                <div><span class="text-white">{{ $name }}</span>: {{ $value[0] }}</div>
            @endforeach
    @endif
</div>
