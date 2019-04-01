#!/bin/bash
#colors
blue='\033[0;34m'
red='\033[0;31m'
green='\033[0;32m'
close='\033[0m'
function init(){
	PEER=$1
	ORG=$2
	CORE_PEER_MSPCONFIGPATH=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org${ORG}.example.com/users/Admin@org${ORG}.example.com/msp
	CORE_PEER_ADDRESS=peer${PEER}.org${ORG}.example.com:7051
	CORE_PEER_LOCALMSPID="Org${ORG}MSP"
	CORE_PEER_TLS_ROOTCERT_FILE=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org${ORG}.example.com/peers/peer${PEER}.org${ORG}.example.com/tls/ca.crt
}
ORDERER_CA=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/ordererOrganizations/example.com/orderers/orderer.example.com/msp/tlscacerts/tlsca.example.com-cert.pem
PEER0_ORG1_CA=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org1.example.com/peers/peer0.org1.example.com/tls/ca.crt
PEER0_ORG2_CA=/opt/gopath/src/github.com/hyperledger/fabric/peer/crypto/peerOrganizations/org2.example.com/peers/peer0.org2.example.com/tls/ca.crt
func=$1
init $2 $3
if [ $func = "invoke" ]; then
	export PRIVATE=$(echo -n "{\"aadhar\":\"123456789\",\"hash\":\"donicbluefire\",\"phone\":\"9440987652\"}" | base64 | tr -d \\n)
	peer chaincode invoke -o orderer.example.com:7050 --tls true --cafile $ORDERER_CA -C mychannel -n mycc --peerAddresses peer0.org1.example.com:7051 --tlsRootCertFiles $PEER0_ORG1_CA --peerAddresses peer0.org2.example.com:7051 --tlsRootCertFiles $PEER0_ORG2_CA -c '{"Args":["Apply","starboy","Deepak H R","01-01-1800"]}'  --transient "{\"private\":\"$PRIVATE\"}"
elif [ $func = "query" ]; then
	echo
	printf "${blue}Querying as $CORE_PEER_ADDRESS\n"
	printf "=======================================${close}\n"
	printf "${green}Public data:${close}\n"
	peer chaincode query -C mychannel -n mycc -c '{"Args":["Query","starboy"]}'
	printf "${red}Private data:${close}\n"
	peer chaincode query -C mychannel -n mycc -c '{"Args":["QueryPrivate","starboy"]}'
	echo
elif [ $func = "channel" ]; then
	peer channel list
fi