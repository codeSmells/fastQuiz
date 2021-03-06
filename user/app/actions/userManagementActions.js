import urlConstants from '../constants/urlConstants.js';
import {serialize} from '../util/util.js';
import {hashHistory} from 'react-router';
import _ from 'lodash';
import {kfetch} from '../util/util.js';
import {triggerNotification} from './notificationActions.js';



export function updatePassword (oldpassword,newpassword) {


	return function (dispatch, getState) {

	kfetch(urlConstants.updatePassword, {

				method :'POST',
				headers: {
					'Content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				body: serialize({"oldpassword":oldpassword,"newpassword":newpassword})
			}).then((response) => {
				return response.json();
			}).then((json)=> {

				if (!json.error) {


					hashHistory.push('dashboard');



				} else {

					if (json.error == "INCORRECT_PASSWORD") {

						dispatch (triggerNotification({
							message : 'Incorrect Password',
							level: 'error'
						}));

					}
				}


			}).catch(() => {

			});

}

}

