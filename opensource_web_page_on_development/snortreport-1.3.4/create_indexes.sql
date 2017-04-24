-- These 4 make an enormous difference as they improve several of the joins used in *every* query in alerts.php
CREATE INDEX ip_cid ON iphdr (cid);
CREATE INDEX udp_cid ON udphdr (cid);
CREATE INDEX tcp_cid ON tcphdr (cid);
CREATE INDEX icmp_cid ON icmphdr (cid);

-- More improvements by using cid indexes:
CREATE INDEX event_cid ON event (cid);
CREATE INDEX data_cid ON data (cid);
	
-- This one makes the two alert using queries using an index instead of a scan.
CREATE INDEX time_sig ON event (timestamp, signature, cid);
