<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/destyle.css@3.0.2/destyle.css">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('select').change(function() {
                var selectedText = $(this).find("option:selected").text();
                var siblingDiv = $(this).siblings('.showContent');
                var thisName = $(this).siblings('.subName');
                var nameHidden = $(this).siblings('.hidden');
                var searchVisible = $(this).siblings('.visible');
                thisName.text(selectedText);
                nameHidden.css("visibility", "visible");
                searchVisible.css("visibility", "hidden");
                if($(this).val() !== "") {
                    siblingDiv.removeClass('standByAnim');
                }
                if(selectedText == "アイズ・ヴァレンシュタイン" || selectedText == "アル・ダハブ=アルカティア"){
                    thisName.css("font-size", "15px");
                }
            });
        });
    </script>
</head>
<body>
    <!-- <header>
        <title>registerMatch</title>
    </header> -->
    <main>
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex">
            <div class="matchBack">
                <div class="flex">
                    <div class="blueLabel">蒼TEAM</div>
                </div>
                <form method="POST" action="{{ route('registerMatch') }}">
                    @csrf
                    <!-- <div class="align_center">
                        <label class="forStay">操作キャラクター</label>
                        <label class="forStay">味方キャラクター</label>
                        <label class="forStay">味方キャラクター</label>
                    </div> -->
                    <div class="align_select">
                        <div class="BlueContent">
                            <div class="standByAnim showContent"></div>
                            <select name="your_character" class="selectForm" required>
                                <option value="" disabled selected></option>
                                @foreach ($characters as $character)
                                    <option value="{{ $character->id }}">{{ $character->name }}</option>
                                @endforeach
                            </select>
                            <label for="selectForm" class="blueName">プレイヤー</label>
                        </div>
                        
                        <div class="BlueContent">
                            <div class="standByAnim showContent"></div>
                            <select name="ally_characters[]" class="selectForm" id="selectForm" required>
                                <option value="" disabled selected></option>
                                @foreach ($characters as $character)
                                    <option value="{{ $character->id }}">{{ $character->name }}</option>
                                @endforeach
                            </select>
                            <label for="selectForm" class="searchLabel visible">検索中</label>
                            <label for="selectForm" class="blueName subName hidden">味方キャラクター</label>
                        </div>
                        
                        <div class="BlueContent">
                            <div class="standByAnim showContent"></div>
                            <select name="ally_characters[]" class="selectForm" id="selectForm" required>
                                <option value="" disabled selected></option>
                                @foreach ($characters as $character)
                                    <option value="{{ $character->id }}">{{ $character->name }}</option>
                                @endforeach
                            </select>
                            <label for="selectForm" class="searchLabel visible">検索中</label>
                            <label for="selectForm" class="blueName subName hidden">味方キャラクター</label>
                        </div>
                        
                    </div>

                    <section class="flex">
                        <div class="matchContent flex">
                            <div class="flex">
                                <label class="">ステージ</label>
                                <select name="stage" class="standByAnim" required>
                                    <option value="" disabled selected></option>
                                    @foreach ($stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                    @endforeach
                                </select>
                                <label class="">結果</label>
                                <input type="radio" name="result" value="win" required class="letterWhite"> Win
                                <input type="radio" name="result" value="lose" required class="letterWhite"> Lose
                                <button class="addButton flex" type="submit">
                                    <div class="Yellow h35 w115 flex">
                                        <div class="YellowContent h27 w107 flex">登録</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </section>

                    <div class="flex">
                        <div class="redLabel">紅TEAM</div>
                    </div>
                    <!-- <div class="align_center">
                        <label class="forStay">敵キャラクター</label>
                        <label class="forStay">敵キャラクター</label>
                        <label class="forStay">敵キャラクター</label>
                    </div> -->

                    <div class="align_select bottom5">
                        <div class="RedContent">
                            <div class="standByAnim showContent"></div>
                            <select name="enemy_characters[]" class="selectForm" id="selectForm" required>
                                <option value="" disabled selected></option>
                                @foreach ($characters as $character)
                                    <option value="{{ $character->id }}">{{ $character->name }}</option>
                                @endforeach
                            </select>
                            <label for="selectForm" class="searchLabel visible">検索中</label>
                            <label for="selectForm" class="redName subName hidden">敵キャラクター</label>
                        </div>

                        <div class="RedContent">
                            <div class="standByAnim showContent"></div>
                            <select name="enemy_characters[]" class="selectForm" id="selectForm" required>
                                <option value="" disabled selected></option>
                                @foreach ($characters as $character)
                                    <option value="{{ $character->id }}">{{ $character->name }}</option>
                                @endforeach
                            </select>
                            <label for="selectForm" class="searchLabel visible">検索中</label>
                            <label for="selectForm" class="redName subName hidden">敵キャラクター</label>
                        </div>

                        <div class="RedContent">
                            <div class="standByAnim showContent"></div>
                            <select name="enemy_characters[]" class="selectForm" id="selectForm" required>
                                <option value="" disabled selected></option>
                                @foreach ($characters as $character)
                                    <option value="{{ $character->id }}">{{ $character->name }}</option>
                                @endforeach
                            </select>
                            <label for="selectForm" class="searchLabel visible">検索中</label>
                            <label for="selectForm" class="redName subName hidden">敵キャラクター</label>
                        </div>
                    </div>


                    
                    
                </form>
            </div>
        </div>

        <div class="flex">
            <a href="{{ route('moveToRate') }}" class="addButton flex">
                <div class="Black h35 w115 flex">
                    <div class="BlackContent h27 w107 flex">戻る</div>
                </div>
            </a>
        </div>
    </main>
    <footer></footer>
</body>
</html>