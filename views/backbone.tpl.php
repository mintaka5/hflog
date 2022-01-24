<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
	    <script src="http://ajax.cdnjs.com/ajax/libs/json2/20110223/json2.js"></script>
	    <script src="http://ajax.cdnjs.com/ajax/libs/underscore.js/1.1.6/underscore-min.js"></script>
	    <script src="http://ajax.cdnjs.com/ajax/libs/backbone.js/0.3.3/backbone-min.js"></script>
	    
	    <script type="text/javascript" src="<?php echo APP_REL_URL; ?>ode/assets/js/backbone/example4.js"></script>
	    
	    <script type="text/javascript">
			//$(function() {
				/*var Item = Backbone.Model.extend({
					defaults:{
						part1:'hello',
						part2:'world'
					}
				});

				var List = Backbone.Collection.extend({
					model:Item
				});

				var ListView = Backbone.View.extend({
					el:$('body'),
					events:{
						'click button#add':'addItem'
					},

					initialize:function() {
						_.bindAll(this, 'render', 'addItem', 'appendItem');

						this.collection = new List();
						this.collection.bind('add', this.appendItem);

						this.counter = 0;
						this.render();
					},

					render: function() {
						var self = this;
						$(this.el).append('<button id="add">Add list item</button>');
						$(this.el).append('<ul></ul>');
						_(this.collection.models).each(function(item) {
							self.appendItem(item);
						}, this);
					},

					addItem:function() {
						this.counter++;
						var item = new Item();
						item.set({
							part2: item.get('part2') + this.counter
						});
						this.collection.add(item);
					},

					appendItem:function(item) {
						$('ul', this.el).append('<li>' + item.get('part1') + ' ' + item.get('part2') + '</li>');
					}
				});*/
				
				/*var ListView = Backbone.View.extend({
					el:$('body'),
					events:{
						'click button#add':'addItem'
					},
					initialize: function() {
						_.bindAll(this, 'render', 'addItem'	);

						this.counter = 0;

						this.render();
					},
					render: function() {
						$(this.el).append('<button id="add">Add list</button>');
						$(this.el).append('<ul></ul>');
					},
					addItem: function() {
						this.counter++;

						$('ul', this.el).append('<li>hello world' + this.counter + '</li>');
					}
				});*/

				//var listView = new ListView();
			//});
	    </script>
	</head>
	<body></body>
</html>