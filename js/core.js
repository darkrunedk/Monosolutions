var app = angular.module('ToDo', []);
app.controller('myCtrl', function($scope, $http) {
	// Form data
	$scope.noteData = {};
	$scope.taskData = {};
	$scope.loginData = {};

	// Data related to the user
	$scope.tasks = [];
	$scope.loggedIn = false;

	// Login
	$scope.login = function() {
		var username = $scope.loginData.username;
		var password = $scope.loginData.password;
		$scope.loginData.username = "";
		$scope.loginData.password = "";

		var data = $.param({
			'username': username,
			'password': password
		});

		var config = {
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		};

		$http.post('ajax/user.php', data, config).then(function success(response){
			console.log(response);

			var data = response.data;
			var status = data.status;
			console.log("Log in: " + status);
			if (status == "success") {
				$scope.loggedIn = true;
				$scope.getTasks();
			}
		});
	};

	// Log out
	$scope.logout = function() {
		var loggedIn = $scope.loggedIn;

		if (loggedIn == true) {
			var data = $.param({
				'logout': true
			});

			var config = {
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			};

			$http.post('ajax/user.php', data, config).then(function success(response){
				var data = response.data;
				var status = data.status;
				console.log("Log in: " + status);
				if (status == "success") {
					$scope.loggedIn = false;
				}
			});
		}
	};

	// Check if a user is already logged in
	$scope.checkLoginStatus = function() {
		$http.get('ajax/user.php').then(function success(response) {
			var data = response.data;
			var status = data.status;
			if (status == "success") {
				$scope.loggedIn = true;
			} else {
				$scope.loggedIn = false;
			}
		});
	};

	// Get tasks for the user
	$scope.getTasks = function() {
		$http.get('ajax/tasks.php').then(function success(response) {
			var data = response.data;
			$scope.tasks = data.content;
		});
	};

	// Add a new note to a task
	$scope.addNote = function(id) {
		var note = $scope.noteData.note;
		$scope.noteData.note = "";

		var data = $.param({
			'newNote': note,
			'taskId': id
		});

		var config = {
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		};

		$http.post('ajax/tasks.php', data, config).then(function success(response){
			var data = response.data;
			console.log("Note: " + data.status);
			if (data.status == "success") {
				$scope.getTasks();
			}
		});
	}

	// Add a new task
	$scope.addTask = function() {
		var taskTitle = $scope.taskData.title;
		$scope.taskData.title = "";

		var data = $.param({
			'title': taskTitle
		});

		var config = {
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		};

		$http.post('ajax/tasks.php', data, config).then(function success(response) {
			var data = response.data;
			var status = data.status;
			console.log("Task: " + status);
			if (status == "success") {
				$scope.getTasks();
			}
		});
	};

	// Mark task as done
	$scope.markDone = function(id, done) {
		var data = $.param({
			'taskId': id,
			'done': done
		});

		var config = {
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		};

		$http.put('ajax/tasks.php', data, config).then(function success(response) {
			var data = response.data;
			console.log("Update: " + data.status);
		});
	};

	// Hide completed tasks
	$scope.hideCompleted = function() {
		$scope.tasks = $scope.tasks.filter(function(item) {
			return !item.done;
		});
	};

	// Check if a user is logged in and get all tasks related to that user.
	$scope.checkLoginStatus();
	$scope.getTasks();
});
