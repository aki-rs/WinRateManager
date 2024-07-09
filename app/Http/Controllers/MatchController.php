<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\Character;
use App\Models\Stage;
use App\Models\MatchCharacter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class MatchController extends Controller
{
    //登録フォーム遷移
    public function moveToRegisterMatch(){
        $characters = Character::all();
        $stages = Stage::all();
        return view('registerMatch',['characters' => $characters,'stages' => $stages]);
    }

    //試合登録用関数
    public function registerMatchFanc(Request $request){
        $validator = Validator::make($request->all(), [
            'your_character' => ['required', 'exists:characters,id'],
            'stage' => ['required', 'exists:stages,id'],
            'result' => ['required', 'in:win,lose'],
            'ally_characters' => ['required', 'array', 'size:2'],
            'ally_characters.*' => ['exists:characters,id'],
            'enemy_characters' => ['required', 'array', 'size:3'],
            'enemy_characters.*' => ['exists:characters,id'],
        ]);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        //試合データ作成
        $match = GameMatch::create([
            'user_id' => Auth::id(),
            'stage_id' => $request->stage,
            'result' => $request->result,
        ]);

        //自分のキャラクターを保存
        MatchCharacter::create([
            'match_id' => $match->id,
            'character_id' => $request->your_character,
            'role' => 'player',
        ]);

        //味方のキャラクターを保存
        foreach($request->ally_characters as $character_id){
            MatchCharacter::create([
                'match_id' => $match->id,
                'character_id' => $character_id,
                'role' => 'ally',
            ]);
        }

        //敵のキャラクターを保存
        foreach($request->enemy_characters as $character_id){
            MatchCharacter::create([
                'match_id' => $match->id,
                'character_id' => $character_id,
                'role' => 'enemy',
            ]);
        }

        return redirect()->route('moveToRate')->with('status', '正常に保存されました。');
    }

    //勝率表示画面遷移用関数
    public function moveToRate(){
        $matches = GameMatch::where('user_id', Auth::id())->with('stage', 'matchCharacters.character')->get();
        $characters = Character::all();
        $stages = Stage::all();

        return view('rate', [
            'matches' => $matches,
            'characters' => $characters,
            'stages' => $stages
        ]);
    }

    //勝率計算用関数
    public function calcWinRate(Request $request){
        $choosen_character = $request->character_id;
        $character = Character::find($choosen_character);
        $matches = GameMatch::where('user_id', Auth::id())->with('matchCharacters.character')->get();
    
        $record = ['player' => ['win' => 0, 'lose' => 0], 'ally' => [], 'enemy' => [], 'stage' => []];
    
        foreach($matches as $match){
            $player = $match->matchCharacters->where('role', 'player')->first();
            if($player && $player->character_id == $choosen_character){
                if($match->result == 'win'){
                    $record['player']['win']++;
                }
                else{
                    $record['player']['lose']++;
                }
    
                foreach($match->matchCharacters as $matchCharacter){
                    if($matchCharacter->role == 'ally'){
                        if(!isset($record['ally'][$matchCharacter->character_id])){
                            $record['ally'][$matchCharacter->character_id] = ['win' => 0, 'lose' => 0];
                        }
    
                        if($match->result == 'win'){
                            $record['ally'][$matchCharacter->character_id]['win']++;
                        }
                        if($match->result == 'lose'){
                            $record['ally'][$matchCharacter->character_id]['lose']++;
                        }
    
                    }
                    if($matchCharacter->role == 'enemy'){
                        if(!isset($record['enemy'][$matchCharacter->character_id])){
                            $record['enemy'][$matchCharacter->character_id] = ['win' => 0, 'lose' => 0];
                        }
                        if($match->result == 'win'){
                            $record['enemy'][$matchCharacter->character_id]['win']++;
                        }
                        if($match->result == 'lose'){
                            $record['enemy'][$matchCharacter->character_id]['lose']++;
                        }
                    }
                }
    
                if(!isset($record['stage'][$match->stage_id])){
                    $record['stage'][$match->stage_id] = ['win' => 0, 'lose' => 0];
                }
                if($match->result == 'win'){
                    $record['stage'][$match->stage_id]['win']++;
                }
                if($match->result == 'lose'){
                    $record['stage'][$match->stage_id]['lose']++;
                }
            }
        }
    
        //自分のキャラクターの勝率
        $playerTotal = $record['player']['win'] + $record['player']['lose'];
        $playerRate = '-';
        if($playerTotal != 0){
            $playerRate = ($record['player']['win'] / $playerTotal) * 100; // playerの勝率
        }
    
        $allCharacters = Character::all();

        // 味方と敵の勝率
        $winRate = [];
    
        foreach($allCharacters as $all){
            $id = $all->id;
            $allyWinRate = '-';
            $enemyWinRate = '-';
    
            if(isset($record['ally'][$id])){
                $allyTotal = $record['ally'][$id]['win'] + $record['ally'][$id]['lose'];
                $allyWinRate = ($record['ally'][$id]['win'] / $allyTotal) * 100;
            }
            if(isset($record['enemy'][$id])){
                $enemyTotal = $record['enemy'][$id]['win'] + $record['enemy'][$id]['lose'];
                $enemyWinRate = ($record['enemy'][$id]['win'] / $enemyTotal) * 100;
            }
    
            $winRate[] = [
                'id' => $id,
                'name' => $all->name,
                'allyWinRate' => $allyWinRate !== '-' ? number_format($allyWinRate, 2) : '-',
                'enemyWinRate' => $enemyWinRate !== '-' ? number_format($enemyWinRate, 2) : '-',
            ];
        }
    
        //ステージの勝率
        $stageWinRate = [];
        $allStages = Stage::all();
    
        foreach($allStages as $stage){
            $st_id = $stage->id;
            $stageWin = '-';
            if(isset($record['stage'][$st_id])){
                $stageTotal = $record['stage'][$st_id]['win'] + $record['stage'][$st_id]['lose'];
                $stageWin = ($record['stage'][$st_id]['win'] / $stageTotal) * 100;
            }
    
            $stageWinRate[] = [
                'id' => $st_id,
                'name' => $stage->name,
                'stageWinRate' => $stageWin !== '-' ? number_format($stageWin, 2) : '-',
            ];
        }
    
        return response()->json([
            'playerWinRate' => $playerRate !== '-' ? number_format($playerRate, 2) : '-',
            'characterWinRates' => $winRate,
            'stageWinRates' => $stageWinRate,
            'characterName' => $character->name,
            'characterId' => $character->id
        ]);
    }

    //リセット用関数
    public function resetMatch(Request $request){
        $user = Auth::user();
        //ユーザー毎の試合取得
        $gameMatches = GameMatch::where('user_id', $user->id)->get();
    
        foreach($gameMatches as $gameMatch){
    
            //選択したキャラクターの試合取得
            $matchCharacters = MatchCharacter::where('match_id', $gameMatch->id)
                                    ->where('character_id', $request->character_id)
                                    ->where('role', "player")
                                    ->get();
    
            foreach($matchCharacters as $matchCharacter){
                $deleteMatchCharacters = MatchCharacter::where('match_id', $matchCharacter->match_id)->get();

                if($deleteMatchCharacters){
                    $deleteGameMatch = GameMatch::where('id', $matchCharacter->match_id)->first();

                    foreach ($deleteMatchCharacters as $deleteMatchCharacter) {
                        $deleteMatchCharacter->delete();
                    }
                    if($deleteGameMatch){
                        $deleteGameMatch->delete();
                    }
                }
            }
        }
    
        return redirect()->route('moveToRate');
    }
    
    
    
}
