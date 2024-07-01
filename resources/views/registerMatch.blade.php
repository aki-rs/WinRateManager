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
            $('#stageSelect').change(function() {
                var selectedStage = $(this).find("option:selected").text();
                $('#selectedStage').text(selectedStage);
            });
            $('select').change(function() {
                //選択したキャラクター名取得
                var selectedText = $(this).find("option:selected").text();
                //検索中アニメーション
                var siblingDiv = $(this).siblings('.showContent');

                //キャラ名のラベル
                var thisName = $(this).siblings('.subName');

                //
                var nameHidden = $(this).siblings('.hidden');

                //検索中表示
                var searchVisible = $(this).siblings('.visible');

                //キャラクター画像
                var characterImg = $(this).siblings('.characterImg')[0];
                var visImg = $(this).siblings('.characterImg');
                var roleImg = $(this).siblings('.roleIcon')[0];
                var visRole = $(this).siblings('.roleIcon');

                // アイコン表示用
                var attacker = [4, 5, 7, 11, 18, 20, 21, 25, 26, 34, 38, 39, 44, 46, 49, 50, 51, 59, 62, 65, 67, 69, 71, 78, 79, 83];
                var gunner = [3, 8, 10, 12, 22, 24, 27, 28, 30, 37, 41, 42, 45, 52, 54, 55, 60, 61, 64, 72, 73, 75, 77, 82];
                var sprinter = [1, 9, 14, 15, 17, 19, 23, 31, 32, 35, 43, 47, 53, 58, 63, 66, 70, 74, 76, 80];
                var tank = [2, 6, 13, 16, 29, 33, 36, 40, 48, 56, 57, 68, 81, 84];
                var selectedValue = parseInt($(this).val(), 10);

                //キャラ名のラベル設定
                thisName.text(selectedText);

                //
                nameHidden.css("visibility", "visible");
                searchVisible.css("visibility", "hidden");
                visImg.css("visibility", "visible");
                visRole.css("visibility", "visible");
                
                if ($(this).val() !== "") {
                    //検索中アニメーション消去
                    siblingDiv.removeClass('standByAnim');

                    //キャラクター画像表示
                    characterImg.src = `{{ asset('storage/images/2/${selectedValue}.png') }}`;
                }

                /*==========================================================

                ロールアイコン表示設定

                ==========================================================*/
                if (attacker.includes(selectedValue)) {
                    roleImg.src = `{{ asset('storage/images/3/attacker.PNG') }}`;
                }
                if (gunner.includes(selectedValue)) {
                    roleImg.src = `{{ asset('storage/images/3/gunner.PNG') }}`;
                }
                if (sprinter.includes(selectedValue)) {
                    roleImg.src = `{{ asset('storage/images/3/sprinter.PNG') }}`;
                }
                if (tank.includes(selectedValue)) {
                    roleImg.src = `{{ asset('storage/images/3/tank.PNG') }}`;
                }
            });
            
            $('input[type=radio]').change(function() {
                var radioId = $(this).attr('id');
                var win1 = document.getElementById("win1");
                var win2 = document.getElementById("win2");
                var lose1 = document.getElementById("lose1");
                var lose2 = document.getElementById("lose2");
                if (radioId === 'win') {
                    win1.classList.add("BlueButton");
                    win1.classList.remove("reBlueButton");
                    win2.classList.add('BlueButtonContent');
                    win2.classList.remove('reBlueButtonContent');
                    lose1.classList.add("reRedButton");
                    lose1.classList.remove("RedButton");
                    lose2.classList.add("reRedButtonContent");
                    lose2.classList.remove("RedButtonContent");
                } else if (radioId === 'lose') {
                    win1.classList.add("reBlueButton");
                    win1.classList.remove("BlueButton");
                    win2.classList.add('reBlueButtonContent');
                    win2.classList.remove("BlueButtonContent");
                    lose1.classList.add("RedButton");
                    lose1.classList.remove("reRedButton");
                    lose2.classList.add("RedButtonContent");
                    lose2.classList.remove("reRedButtonContent");
                }
            });
        });
    </script>
</head>
<body>
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
                <div class="flexStart">
                    <div class="headerPart letterWhite">マッチング登録</div>
                </div>

                <!--==========================================================

                    蒼TEAM表示

                ==========================================================-->
                <div class="flex">
                    <div class="blueLabel">蒼TEAM</div>
                </div>
                <form method="POST" action="{{ route('registerMatch') }}" autocomplete="off">
                    @csrf
                    <div class="align_select">

                        <!--==========================================================

                            自キャラ

                        ==========================================================-->
                        <div class="BlueContent">
                            <div class="standByAnim showContent"></div>
                            <img src="" class="roleIcon">
                            <img src="" class="characterImg">
                                <select name="your_character" class="selectForm" required>
                                    <option value="" disabled selected></option>
                                    @foreach ($characters as $character)
                                        <option value="{{ $character->id }}">{{ $character->name }}</option>
                                    @endforeach
                                </select>
                            </img>
                            <label for="selectForm" class="blueName">プレイヤー</label>
                        </div>
                        
                        <!--==========================================================

                            味方1

                        ==========================================================-->
                        <div class="BlueContent">
                            <div class="standByAnim showContent"></div>
                            <img src="" class="roleIcon">
                            <img src="" class="characterImg">
                                <select name="ally_characters[]" class="selectForm" required>
                                    <option value="" disabled selected></option>
                                    @foreach ($characters as $character)
                                        <option value="{{ $character->id }}">{{ $character->name }}</option>
                                    @endforeach
                                </select>
                            </img>
                            <label for="selectForm" class="searchLabel visible">検索中</label>
                            <label for="selectForm" class="blueName subName hidden">味方キャラクター</label>
                        </div>

                        <!--==========================================================

                            味方2

                        ==========================================================-->
                        <div class="BlueContent">
                            <div class="standByAnim showContent"></div>
                            <img src="" class="roleIcon">
                            <img src="" class="characterImg">
                                <select name="ally_characters[]" class="selectForm" required>
                                    <option value="" disabled selected></option>
                                    @foreach ($characters as $character)
                                        <option value="{{ $character->id }}">{{ $character->name }}</option>
                                    @endforeach
                                </select>
                            </img>
                            <label for="selectForm" class="searchLabel visible">検索中</label>
                            <label for="selectForm" class="blueName subName hidden">味方キャラクター</label>
                        </div>
                    </div>

                    <!--==========================================================

                        中間部分

                    ==========================================================-->
                    <section class="flex">
                        <div class="matchContent flex">
                            <div class="flexBetween">
                                <div class="addButton flex">
                                    <div class="Black h36 w116 flex">
                                        <div class="BlackContent h28 w108 flex">
                                            <div class="selectContainer">
                                                <select name="stage" id="stageSelect" required>
                                                    <option value="" disabled selected>-</option>
                                                    @foreach ($stages as $stage)
                                                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <label class="">勝利TEAM</label>
                                <input type="radio" name="result" value="win" id ="win" required>
                                <div class="flex">
                                    <div class="buttonCover h40 w40 flex">
                                        <div class="reBlueButton h36 w36 flex win1" id="win1">
                                            <label class="reBlueButtonContent h28 w27 flex win2" for="win" id="win2">蒼</label>
                                        </div>
                                    </div>
                                </div>

                                <input type="radio" name="result" value="lose" id="lose" required>
                                <div class="flex">
                                    <div class="buttonCover h40 w40 flex">
                                        <div class="reRedButton h36 w36 flex lose1" id="lose1">
                                            <label class="reRedButtonContent h28 w27 flex lose2" for="lose" id="lose2">紅</label>
                                        </div>
                                    </div>
                                </div>

                                <button class="addButton flex" type="submit">
                                    <div class="Yellow h36 w116 flex">
                                        <div class="YellowContent h28 w108 flex">登録</div>
                                    </div>
                                </button>
                            </div>

                            <div class="stageShowPart">
                                <div>選択ステージ</div>
                                <div class="" id="selectedStage">-</div>
                            </div>

                        </div>
                    </section>

                    <!--==========================================================

                        紅TEAM表示

                    ==========================================================-->
                    <div class="flex">
                        <div class="redLabel">紅TEAM</div>
                    </div>
                    <div class="align_select bottom5">

                        <!--==========================================================

                            相手1

                        ==========================================================-->
                        <div class="RedContent">
                            <div class="standByAnim showContent"></div>
                            <img src="" class="roleIcon">
                            <img src="" class="characterImg">
                                <select name="enemy_characters[]" class="selectForm" required>
                                    <option value="" disabled selected></option>
                                    @foreach ($characters as $character)
                                        <option value="{{ $character->id }}">{{ $character->name }}</option>
                                    @endforeach
                                </select>
                            </img>
                            <label for="selectForm" class="searchLabel visible">検索中</label>
                            <label for="selectForm" class="redName subName hidden">敵キャラクター</label>
                        </div>

                        <!--==========================================================

                            相手2

                        ==========================================================-->
                        <div class="RedContent">
                            <div class="standByAnim showContent"></div>
                            <img src="" class="roleIcon">
                            <img src="" class="characterImg">
                                <select name="enemy_characters[]" class="selectForm" required>
                                    <option value="" disabled selected></option>
                                    @foreach ($characters as $character)
                                        <option value="{{ $character->id }}">{{ $character->name }}</option>
                                    @endforeach
                                </select>
                            </img>
                            <label for="selectForm" class="searchLabel visible">検索中</label>
                            <label for="selectForm" class="redName subName hidden">敵キャラクター</label>
                        </div>

                        <!--==========================================================

                            相手3

                        ==========================================================-->
                        <div class="RedContent">
                            <div class="standByAnim showContent"></div>
                            <img src="" class="roleIcon">
                            <img src="" class="characterImg">
                                <select name="enemy_characters[]" class="selectForm" required>
                                    <option value="" disabled selected></option>
                                    @foreach ($characters as $character)
                                        <option value="{{ $character->id }}">{{ $character->name }}</option>
                                    @endforeach
                                </select>
                            </img>
                            <label for="selectForm" class="searchLabel visible">検索中</label>
                            <label for="selectForm" class="redName subName hidden">敵キャラクター</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="flex mt10">
            <a href="{{ route('moveToRate') }}" class="addButton flex">
                <div class="Black h36 w116 flex">
                    <div class="BlackContent h28 w108 flex">戻る</div>
                </div>
            </a>
        </div>
    </main>
    <footer></footer>
</body>
</html>
