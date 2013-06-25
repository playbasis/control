using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using pblib.NET;
using System.Diagnostics;

namespace test.pblib.NET
{
	class Program
	{
		private static void print(string str)
		{
			dynamic obj = Playbasis.JsonToDynamic(str);
			Console.WriteLine(obj);
		}
		static void Main(string[] args)
		{
			var pb = new Playbasis();
			if(!pb.auth("abc", "abcde"))
				Debug.Assert(false);
			string id = "1";

			/*
			print(pb.player(id));
			//print(pb.register("usr1", "test1", "test@email.com", "http://imageurl.jpg", "facebook_id=123456", "first_name=testuser"));
			print(pb.login(id));
			print(pb.logout(id));
			print(pb.points(id));
			print(pb.point(id, "exp"));
			print(pb.actionLastPerformed(id));
			print(pb.actionLastPerformedTime(id, "like"));
			print(pb.actionPerformedCount(id, "like"));
			print(pb.badgeOwned(id));
			print(pb.rank("exp", 10));
			print(pb.ranks(5));
			print(pb.badges());
			print(pb.badge("1"));
			print(pb.badgeCollections());
			print(pb.badgeCollection("1"));
			print(pb.actionConfig());
			*/

			for (int i = 0; i < 20; ++i)
			{
				print(pb.rule(id, "like"));
				Console.ReadKey();
			}
			return;
		}
	}
}
