

<!--#include file="../www.aionchs.com/Inc/conn.asp"-->

<title>��ͥ�����̸</title>
<link rel='stylesheet' href='/images/style.css' type='text/css'>

<div align="center">

<TABLE cellSpacing=1 cellPadding=6 width=" & tee5.TabWidth & " align=center class=tableBorder1 border=0>
<tr>
<th>����ͥ��С�</th>
<th>������Ҿӡ�</th>
<th>��������Ϸ��</th>
<th>����ҳ��Ϸ�����ĵá�</th>
</tr>
<tr>
<td width="25%" bgcolor="#000000" class="you"><TABLE cellSpacing=1 cellPadding=1 width="100%" border=0 align="center">

<%
set rs=conn.execute("select top 50 id,classid,classname,topic from NewsList where classid=1 order by classname DESC,ID DESC")
do while not rs.eof
%>
<li>[<a class='l2' href='News/<%=rs("classid")%>.htm' target='_blank'><%=rs("classname")%></a>]<a class='l4' href='News/Detail<%=rs("id")%>.htm'><%=left(rs("topic"),20)%><%if len(rs("topic"))>20 then%>...<%end if%></a></li>
<%
rs.movenext
loop
set rs=nothing
%>
      </table>
	  </td>

<td width="25%" bgcolor="#FFFFFF" class="you"><TABLE cellSpacing=1 cellPadding=1 width="100%" border=0 align="center">
<%
set rs=conn.execute("select top 50 id,classid,classname,topic from NewsList where classid=2 order by classname DESC,ID DESC")
do while not rs.eof
%>
<li>[<a class='l2' href='News/<%=rs("classid")%>.htm' target='_blank'><%=rs("classname")%></a>]<a class='l4' href='News/Detail<%=rs("id")%>.htm'><%=left(rs("topic"),20)%><%if len(rs("topic"))>20 then%>...<%end if%></a></li>
<%
rs.movenext
loop
set rs=nothing
%>
      </table>
	  </td>


<td width="25%" bgcolor="#FFFFFF" class="you"><TABLE cellSpacing=1 cellPadding=1 width="100%" border=0 align="center">
<%
set rs=conn.execute("select top 50 id,classid,classname,topic from NewsList where classid=3 order by classname DESC,ID DESC")
do while not rs.eof
%>
<li>[<a class='l2' href='News/<%=rs("classid")%>.htm' target='_blank'><%=rs("classname")%></a>]<a class='l4' href='News/Detail<%=rs("id")%>.htm'><%=left(rs("topic"),20)%><%if len(rs("topic"))>20 then%>...<%end if%></a></li>
<%
rs.movenext
loop
set rs=nothing
%>
      </table>
	  </td>

<td width="25%" bgcolor="#FFFFFF" class="you"><TABLE cellSpacing=1 cellPadding=1 width="100%" border=0 align="center">
<%
set rs=conn.execute("select top 50 id,classid,classname,topic from NewsList where classid=4 order by classname DESC,ID DESC")
do while not rs.eof
%>
<li>[<a class='l2' href='News/<%=rs("classid")%>.htm' target='_blank'><%=rs("classname")%></a>]<a class='l4' href='News/Detail<%=rs("id")%>.htm'><%=left(rs("topic"),20)%><%if len(rs("topic"))>20 then%>...<%end if%></a></li>
<%
rs.movenext
loop
set rs=nothing
%>
      </table>
	  </td>





</body>
</html>
