weAddToCollection = {
	conf: {
		table: '',
		targetInsertIndex: '',
		targetInsertPos: -1
	},
	g_l: {
		nothingToMove: '',
		notValidFolder: ''
	},
	init: function (conf, g_l, we_const) {
		this.conf = conf;
		this.g_l = g_l;
		this.we_const = we_const;

		top.treeData.setstate(top.treeData.tree_states.select);
		if (top.treeData.table != this.conf.table) {
			top.treeData.table = this.conf.table;
			this.we_cmd("load", this.conf.table);
		} else {
			this.we_cmd("load", this.conf.table);
			top.drawTree();
		}
	},
	press_ok_add: function () {
		var sel = '';
		for (var i = 1; i <= top.treeData.len; i++) {
			if (top.treeData[i].checked == 1) {
				sel += (top.treeData[i].id + ",");
			}
		}
		if (!sel) {
			top.toggleBusy(0);
			top.we_showMessage(this.g_l.nothingToMove, WE().consts.message.WE_MESSAGE_NOTICE, window);
			return;
		}

		// check if selected target exists
		var acStatus = '';
		var invalidAcFields = false;
		acStatus = YAHOO.autocoml.checkACFields();
		acStatusType = typeof acStatus;
		if (acStatusType.toLowerCase() == 'object') {
			if (acStatus.running) {
				setTimeout(press_ok_move, 100);
				return;
			} else if (!acStatus.valid) {
				top.we_showMessage(this.g_l.notValidFolder, WE().consts.message.WE_MESSAGE_NOTICE, window);
				return;
			}
		}

		// check if collection is open
		var _usedEditors = top.weEditorFrameController.getEditorsInUse(),
						_collID = document.getElementById('yuiAcResultDir').value,
						_isOpen = false,
						_isEditorCollActive = false,
						_frameId,
						_transaction,
						_editor,
						_contentEditor;

		_collID = _collID ? _collID : 0;
		for (_frameId in _usedEditors) {
			_editor = _usedEditors[_frameId];
			if (_editor.getEditorEditorTable() == WE().consts.tables.VFILE_TABLE && _editor.getEditorDocumentId() == _collID) {
				_isOpen = true;
				_transaction = _editor.getEditorTransaction();
				if (_editor.getEditorEditPageNr() == 1) {
					_isEditorCollActive = true;
					_contentEditor = _editor.getContentEditor();
				} else {
					_editor.setEditorIsHot(true);
				}
			}
		}

		if (_isOpen) {
			if (_isEditorCollActive) {

				var onInsertClose = false;

				if (!this.conf.targetInsertIndex) {// opened from menu or from collection head
					var ct = _contentEditor.document.getElementById('content_div_' + _contentEditor.weCollectionEdit.view),
									collectionArr = _contentEditor.weCollectionEdit.collectionArr,
									index = collectionArr[collectionArr.length - 1];

					for (var j = collectionArr.length - 1; j >= 0; j--) {
						if (collectionArr[j] === -1) {
							index = j;
						} else {
							break;
						}
					}
					this.conf.targetInsertIndex = ct.childNodes[index].id.substr(10);
				} else {
					onInsertClose = true;
				}

				_contentEditor.weCollectionEdit.callForValidItemsAndInsert(this.conf.targetInsertIndex, sel, true);
				_editor.setEditorIsHot(true);

				if (onInsertClose) {
					this.we_cmd('exit_addToCollection', '', 'we65_tblFile');
				} else {
					for (var i = 1; i <= top.treeData.len; i++) {
						if (top.treeData[i].constructor.name === 'node' && top.treeData[i].checked) {
							top.checkNode('img_' + top.treeData[i].id);
						}
					}
				}

				return;
			}
			document.we_form.we_targetTransaction.value = _transaction;
			top.we_cmd('exit_addToCollection', '', 'we65_tblFile');
		}

		this.we_cmd('do_addToCollection', '', this.conf.table);
	},
	we_submitForm: function (target, url) {
		var f = self.document.we_form;
		var sel = "";
		for (var i = 1; i <= top.treeData.len; i++) {
			if (top.treeData[i].checked == 1) {
				sel += (top.treeData[i].id + ",");
			}
		}
		if (!sel) {
			top.toggleBusy(0);
			top.we_showMessage(this.g_l.nothingToMove, WE().consts.message.WE_MESSAGE_NOTICE, window);
			return false;
		}

		sel = sel.substring(0, sel.length - 1);
		f.sel.value = sel;
		f.target = target;
		f.action = url;
		f.method = "post";
		f.submit();
	},
	we_cmd: function () {
		var args = [];
		for (var i = 0; i < arguments.length; i++)
		{
			args.push(arguments[i]);
		}
		if (parent.we_cmd) {
			parent.we_cmd.apply(this, args);
		}
	}
};

function we_submitForm(target, url) {
	weAddToCollection.we_submitForm(target, url);
}