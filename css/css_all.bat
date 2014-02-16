
del style_dhblog.css style_daohang.css style.css 
copy base.css+layout.css+title.css+layout_tab.css+entry.css+article_index.css+page_navi.css+page_class.css style.css  /Y /B
csstidy.exe style.css --template=highest --remove_last_;=true style.css

copy base.css+layout.css+title.css+layout_tab.css style_daohang.css  /Y /B
csstidy.exe style_daohang.css --template=highest --remove_last_;=true style_daohang.css
copy style_daohang.css ..\..\daohang\css\style.css /Y /B

copy base.css+title.css+article_index.css+page_navi.css+page_class.css+layout_dhblog.css+postbox.css+side_dhblog.css+entry_dhblog.css style_dhblog.css  /Y /B
csstidy.exe style_dhblog.css --template=highest --remove_last_;=true style_dhblog.css
copy style_dhblog.css ..\..\dhblog\css\style.css /Y /B