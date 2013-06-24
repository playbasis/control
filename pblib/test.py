from playbasis import Playbasis
import pprint

pb = Playbasis()
pp = pprint.PrettyPrinter(indent=4)
res = pb.auth('abc', 'abcde')
print res
res = pb.ranks(5)
pp.pprint(res)
