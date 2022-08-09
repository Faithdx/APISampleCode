<%@ Page Language="C#" AutoEventWireup="true" Async="true" CodeBehind="ApiTest.aspx.cs" Inherits="AspNet.ApiTest1" %>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
</head>
<body>
    <form id="form1" runat="server">
        <asp:Button ID="Button1" runat="server" Text="获取账户余额" OnClick="Button1_Click" />
        <br />
        <div>
            <p>返回结果：</p>
            <asp:Label ID="Label1" runat="server" BackColor="#33CCFF" Text="第一个ASP.NET程序"></asp:Label>
        </div>
        <br />
        <asp:Button ID="AspTest" Text="ASP Test" runat="server" OnClick="Asp_Click" />
    </form>
</body>
</html>
