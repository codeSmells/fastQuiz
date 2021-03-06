<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use stdClass;
use Session;
use App\Levels;
use App\Options;
use App\Questions;
use App\User;

class SubmitLevelController extends Controller {
	
	public static function index() {

		if(Session::has('username')){
			$payload = Input::all();

			$payLoadAnswers = $payload['answers'];
			$currentLevel = $payload['level'];


			$currentLevelQIds = Questions::getQuestionIdsOfLevel($currentLevel);
			foreach ($payLoadAnswers as $answer) {
				if (!in_array($answer['questionId'], $currentLevelQIds)){
					return \Response::json(array('ERROR'=>"QUESTION NOT IN CURRENT LEVEL"));
				}
			}

	//check if the question ids given are valid question ids or not

			$correctOptionIds = Options::getCorrectOptionIds($payLoadAnswers); 
			//return $correctOptionIds;
			$countOfCorrect = 0;
			$countOfTotal = 0;
			$obj1 = new stdClass();
			$obj1->correctAnswers = array ();

			$questionIdArr = array();
			foreach ($payLoadAnswers as $answer){
				array_push($questionIdArr, array('questionId' => $answer['questionId'], 'optionId' => $answer['optionId']));
			}
			asort($questionIdArr);
			
			foreach ($questionIdArr as $answer){
				$countOfTotal++;
				$dbElement = $correctOptionIds[$answer['questionId']];
				$payloadElement = $answer['optionId'];
				if($dbElement == $payloadElement){
					$countOfCorrect++;
				}
				
			    array_push($obj1->correctAnswers, array (

			    		"questionId" => $answer['questionId'],
			    		"correctAnswerOptionId" => $dbElement 
			    	));
			}

			$percent = $countOfCorrect/$countOfTotal;
			

			if(Session::has('score')){

				$updatedScore = Session::get('score') + $countOfCorrect;

				Session::put('score', $updatedScore);

			}
			else {
				//echo 'entered else';
				Session::put('score', $countOfCorrect);
			}


			
			$nextLevel = $currentLevel + 1;
			$maxLevel = Levels::getMaxLevel();
			$totalScore = Session::get('score');


			$obj = new stdClass();
			if($currentLevel == $maxLevel){
				$obj->isGameOver = true;
				$updateRes = User::updateUserMaxLevelandScore(Session::get('username'), Session::get('score'), $currentLevel);
			} else {
				$obj->isGameOver = false;
			}
			$obj->score = $countOfCorrect;
			$obj->totalScore = $totalScore;
			$obj->totalNoOfQuestionsInCurrentLevel = $countOfTotal;
			$obj->previous = array('correctAnswers' => $obj1->correctAnswers);

			if($percent > 0.5){
				$obj->hasQualified = true;
				$obj->next = Questions::getQuestionsOfLevel($nextLevel);
				$updateRes = User::updateUserMaxLevelandScore(Session::get('username'), Session::get('score'), $currentLevel);
				return \Response::json($obj);
			}
			else{
				$obj->hasQualified = false;
				$updateRes = User::updateUserMaxLevelandScore(Session::get('username'), Session::get('score'), $currentLevel);
				return \Response::json($obj);
			}
		}
		else {
			return Response(json_encode(["error" => "SESSION_DOES_NOT_EXIST"]));	
		}
	}
}

