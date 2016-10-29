import React from 'react';
import CSSModules from 'react-css-modules';
import GameStyle from './assets/GameComponent.scss';
import ActiveQuizComponent from './ActiveQuizComponent/ActiveQuizComponent.js';
import MidLevelComponent from './MidLevelComponent/MidLevelComponent.js';

class GameComponent extends React.Component {

	constructor (props) {

		super(props);

	}

	componentWillMount () {

		let {fetchNewGame} =  this.props.gameActions;
		fetchNewGame ();
	}	

	getTypeOfComponentToLoad () {

		switch (this.props.game.status.gameComponentScreenType) {

			case 'ACTIVE_QUIZ' : return ActiveQuizComponent;
								break;

			case 'MID_LEVEL' : 	return MidLevelComponent;
								break;
			default : return ActiveQuizComponent;
		}


	}


	getComponentProps () {


		switch (this.props.game.status.gameComponentScreenType) {

			case 'ACTIVE_QUIZ' : return {game:this.props.game, actions: this.props.gameActions}
								break;
			case 'MID_LEVEL' :  return {status: this.props.game.status, actions : this.props.gameActions};
								break;
			default : return {game:this.props.game, actions: this.props.gameActions}
		}

	}
	render () {

		let ComponentToBeLoaded =  this.getTypeOfComponentToLoad();
		let propsToBePassed = this.getComponentProps ();

		return (
			<div>
				<ComponentToBeLoaded {...propsToBePassed} />
			</div>



			);
		

	}
}

export default CSSModules(GameComponent, GameStyle);