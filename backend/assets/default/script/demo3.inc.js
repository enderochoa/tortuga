function createjsDOMenu() {
  absoluteMenu1 = new jsDOMenu(120, "absolute");
  with (absoluteMenu1) {
    addMenuItem(new menuItem("Item 1", "", "blank.htm"));
    addMenuItem(new menuItem("Item 2", "item2", "blank.htm"));
    addMenuItem(new menuItem("Item 3", "", "blank.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 4", "", "blank.htm"));
    addMenuItem(new menuItem("Item 5", "", "blank.htm"));
  }
  
  absoluteMenu1_1 = new jsDOMenu(130, "absolute");
  with (absoluteMenu1_1) {
    addMenuItem(new menuItem("Item 1", "", "blank.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 2", "", "blank.htm"));
    addMenuItem(new menuItem("Item 3", "", "blank.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 4", "", "blank.htm"));
  }
  
  absoluteMenu2 = new jsDOMenu(120, "absolute");
  with (absoluteMenu2) {
    addMenuItem(new menuItem("Item 1", "", "blank.htm"));
    addMenuItem(new menuItem("Item 2", "", "blank.htm"));
    addMenuItem(new menuItem("Item 3", "", "blank.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 4", "item4", "blank.htm"));
  }
  
  absoluteMenu2_1 = new jsDOMenu(150, "absolute");
  with (absoluteMenu2_1) {
    addMenuItem(new menuItem("Item 1", "", "blank.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 2", "", "blank.htm"));
    addMenuItem(new menuItem("Item 3", "", "blank.htm"));
    addMenuItem(new menuItem("Item 4", "", "blank.htm"));
    addMenuItem(new menuItem("Item 5", "", "blank.htm"));
  }
  
  absoluteMenu3 = new jsDOMenu(140, "absolute");
  with (absoluteMenu3) {
    addMenuItem(new menuItem("Item 1", "item1", "blank.htm"));
    addMenuItem(new menuItem("Item 2", "", "blank.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 3", "", "blank.htm"));
    addMenuItem(new menuItem("Item 4", "", "blank.htm"));
  }
  
  absoluteMenu3_1 = new jsDOMenu(150, "absolute");
  with (absoluteMenu3_1) {
    addMenuItem(new menuItem("Item 1", "", "blank.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 2", "", "blank.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 3", "", "blank.htm"));
    addMenuItem(new menuItem("-"));
    addMenuItem(new menuItem("Item 4", "", "blank.htm"));
    addMenuItem(new menuItem("Item 5", "", "blank.htm"));
  }
  
  absoluteMenu1.items.item2.setSubMenu(absoluteMenu1_1);
  absoluteMenu2.items.item4.setSubMenu(absoluteMenu2_1);
  absoluteMenu3.items.item1.setSubMenu(absoluteMenu3_1);
  
  absoluteMenuBar = new jsDOMenuBar("absolute", "", true);
  with (absoluteMenuBar) {
    addMenuBarItem(new menuBarItem("Item 1", absoluteMenu1));
    addMenuBarItem(new menuBarItem("Item 2", absoluteMenu2));
    addMenuBarItem(new menuBarItem("Item 3", absoluteMenu3));
    moveTo(10, 130);
  }
}