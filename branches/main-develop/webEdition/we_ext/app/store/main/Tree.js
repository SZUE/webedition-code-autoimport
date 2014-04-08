/**
 * webEdition CMS
 *
 * $Rev: 7210 $
 * $Author: lukasimhof $
 * $Date: 2013-12-28 14:19:02 +0100 (Sa, 28 Dez 2013) $
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_EXT
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

Ext.define('WE.store.main.Tree', {
	extend: 'Ext.data.TreeStore',
	timestamp: 0,
	compareBlocked: false,
	//timestamps: {},

	config: {
		//model: 'WE.model.main.Tree',
		fields: [
			{ name: 'id', type: 'integer' },
			{ name: 'text', type: 'string' },
			{ name: 'table', type: 'string' },
			//{ name: 'isTblFile', type: 'boolean' },
			{ name: 'ct', type: 'string' },
			//{ name: 'doReload', type: 'boolean' },
			{ name: 'timestamp', type: 'integer' },
			{ name: 'qtip', type: 'integer' }
		],
		autoLoad: true,
		nodeParam: 'we_cmd[2]',
		folderSort: true,
		sorters: [{property:'text', direction: 'ASC'}],
		sortOnLoad: true,
		table: '',
		isDeleteMode: false,
		lastNodeClicked: null, 
		treePane: {},
		timestamps: {}
	},

	constructor: function(config) {
		this.initConfig(config);

		Ext.apply(this,{
			id: 'tree_' + this.table,
			storeId: 'tree_' + this.table,
			proxy: {
				type: 'ajax',
				url: 'we_cmd_ext.php?we_cmd[0]=load_main_tree&we_cmd[1]=' + this.table,
				extraParams: {
					//
				},
				reader: {
					type: 'json',
					root: 'items'
					//successProperty: 'success'
				},
				//using autoload there's no callback, and listener load does not get response...
				//success: this.setTimestampOnLoad,
				scope: this
			},
			root: {
				text: 'Ext JS',
				id: 0
			},
			listeners: {
				load: {
					fn: function(tree, node, records, success) {
						this.setCompareBlocked(false);
						if(records.length > 0){
							this.timestamp = records[0].data.timestamp;
							this.timestamps[this.id + '_' + node.id] = records[0].data.timestamp;
						}
					}
				},
				scope: this
				
			}
		});
		this.callParent(arguments);

		//register instance to static member instances
		this.self.registerInstance(this.table, this);
	},

	setTimestampOnLoad: function(response, request){//obsolete?
		//console.log(response);
	},

	setItemState: function(id){
		//
	},
			
	updateNode: function(record){
		//IMPORTANT: fn exspects record of type main_store model, not tree model
		//=> we should change this and map records to tree model before calling fn
		//=> or: use model type to map different model instances inside fn

		if(record.data['we_id'] == 0){
			Ext.Error.raise({
				msg: 'WE_ID = 0!'
			});
		}

		var node = this.getNodeById(record.data['we_id']),
			cls =  record.data.isFolder ? 'tree_item_bold' : 
				(Ext.isString(record.data['cls']) ? record.data['cls'] : 
					(record.data['published'] === '0' || record.data['published'] === 0 ? 'tree_item_notpublished' : 
						(record.data['published'] === -1 ? 'tree_item_changed' : 
							'')));

		if(typeof node !== 'undefined'){//console.log('node exists');
			//if node exists, only text, cls and parentid could have changed
			node.beginEdit();
			node.set('text', record.data['text']);
			node.set('cls', cls);
			node.endEdit();

			//move node if parentid has changed
			if(record.data['parentid'] !== node.get('parentId')){//console.log('move node');
				var parent = this.getNodeById(record.data['parentid']);
				parent = typeof parent !== 'undefined' ? parent : this.root;
				this.getNodeById(record.data['parentid']).appendChild(node);
				this.sort();
			}
		} else {//console.log('add new node');
			var parentNode = this.getNodeById(record.data['parentid']);
			if(Ext.isObject(parentNode)){
				//TODO: have an array with all mandatory fields listed to iterate on (or iterate over model fields)
				parentNode.appendChild({
					'id': record.data['we_id'],
					'leaf' : (record.data['isFolder'] ? false : true),
					'text': record.data['text'],
					'table': record.data['table'],
					'ct': record.data['ct'],
					'time': record.data['ct'],
					'expanded': record.data['expanded'],
					'disabled': record.data['disabled'],
					'iconCls': record.data['iconCls'],
					'cls': cls, 
					'qtip': record.data['we_id']
				});
				this.sort();
			}
		}
	},

	checkRange: function(node){
		if(this.lastNodeClicked === node){
			return;
		}

		this.lastNodeClicked = this.lastNodeClicked === null ? this.getRootNode() : this.lastNodeClicked;
		var newVal = ((!this.lastNodeClicked.get('checked') || node.get('checked')) && this.lastNodeClicked !== this.getRootNode()) ? false : true;
		var doCheck = false;

		this.getRootNode().cascadeBy(function(n){
			Ext.suspendLayouts();
			if(doCheck){
				if(!n.data.leaf && !n.get('checked')){
					n.cascadeBy(function(inner){
						inner.set('checked', newVal);
					});
				}
				n.set('checked', newVal);
				if(!newVal){
					this.uncheckParentRecursive(n);
				}

				doCheck = (n === node || n === this.lastNodeClicked) ? false : doCheck;
				if(!doCheck){
					Ext.resumeLayouts();
					return false;
				}
			} else {
				doCheck = (n === node || n === this.lastNodeClicked) ? true : doCheck;
				if(doCheck && !newVal){
					this.uncheckParentRecursive(n);
				}
			}
			Ext.resumeLayouts();
		}, this);
	},

	uncheckParentRecursive: function(node){
		Ext.suspendLayouts();
		node.bubble(function(n){
			if(!n.get('checked') || n === this.getRootNode()){
				Ext.resumeLayouts();
				return false;
			}
			n.set('checked', false);
		}, this);
	},
			
	checkChildrenRecursive: function(node){
		node.cascadeBy(function(n){
				Ext.suspendLayouts();
				n.set('checked', true);
				Ext.resumeLayouts();
		});
	},

	setAllCheckboxes: function(show){
		Ext.suspendLayouts();
		this.getRootNode().cascadeBy(function(n){
			n.set('checked', show);
		});
		Ext.resumeLayouts(true);
	},

	setCompareBlocked: function(blocked){
		this.compareBlocked = blocked;
	},

	compareTreeWithServer: function(){//TODO: better name: getCompareFromDbAndSync
		//TODO: add to rpc: boolean singleUser
			//here: if(single[nodeId] === true || single[nodeId] === 'noping') no need to compare;
			//but important: must be singeUser all since last timestamp!:
			//a) onsetting timestamp[nodeId] (set single[nodeId] = 'noping')
			//b) onping single[nodeId] = single[nodeId] === false ? false : singleUser (einmal falsch = immer falsch!!

		//TODO: take last config of store proxy + timestamp and node!
		//TODO: make this for different folders. timestamps are allready here!
		if(!this.compareBlocked){
		Ext.Ajax.request({
			url: 'we_cmd_ext.php?we_cmd[0]=check_update_tree&we_cmd[1]=' + this.table,
			params: {
				'we_cmd[2]': 0,
				'timestamp': this.timestamps[this.id + '_' + 0]
			},
			reader: {
				type: 'json',
				root: 'items'
			},
			success: this.syncronizeTree,
			failure: function() {},
			scope: this
		});
		}
	},

	syncronizeTree: function(response, request){
		//IMPORTANT: check_tree items of type main store record (data, id = we_id ...)
		var decodedResponse = Ext.JSON.decode(response.responseText),
			compareItems = decodedResponse.compareItems,
			timestamp = decodedResponse.timestamp,
			compareRoot = request.params['we_cmd[2]'];	//TODO: write to response
														//and use this instead of rootNode

		this.timestamps[this.id + '_' + compareRoot] = timestamp;

		/* not too efficient but working */
		Ext.Object.each(compareItems, function(key, item, myself){
			if(item){
				this.updateNode(item);
			}
		}, this );

		var remArr = [];
		this.getRootNode().cascadeBy(function(n){
			if(Ext.isEmpty(compareItems[n.data.id])){
				if(n.data.id !== 0){
					remArr.push(n);
				}
			}
		});

		if(!Ext.isEmpty(remArr)){
			for(i=0; i<remArr.length; i++){
				remArr[i].remove(true);
			}
		}
		/* end */		

		/* more efficient but not working yet
		//iterate over all nodes in tree store
		var remArr = [], i = 0;
		this.getRootNode().cascadeBy(function(n){
			if(Ext.isObject(compareItems[n.data.id])){
				if(compareItems[n.data.id].data.mod){
					this.updateNode(compareItems[n.data.id]);
				}
				delete compareItems[n.data.id];
			} else {
				remArr.push(n);
			}
		});

		//remove nodes not found in compareItems
		if(!Ext.isEmpty(remArr)){
			for(i=0; i<remArr.length; i++){
				remArr[i].remove(true);
			}
		}
		
		//add nodes from compareItems not yet deleted by cascading tree (new items!)
		Ext.Object.each(compareItems, function(key, item, myself){
			this.updateNode(compareItems[item]); 
		});
		*/
		Ext.getCmp('tree_' + this.table).el.unmask();
	},

	statics : {
		instances: {},
		registerInstance: function(table, obj){
			//IMPORTANT: this in statics refers to the class, not an instance
			this.instances[table] = obj;
		},
		getTreeByTable: function(table){
			var tree = this.instances[table];
			
			//TODO: check Ext's utils for conditions like typeof...
			return (typeof tree !== 'undefined' ? tree : false);
		}
	}

});