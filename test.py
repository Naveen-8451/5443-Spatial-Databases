from pyspark.conf import SparkConf
from pyspark.context import SparkContext
from pyspark.sql.context import SQLContext
from pyspark.sql.context import HiveContext
from caqfmodel.utils import model_job_util
from common import oracleutility
from common import utils
import os
from common import constants
from common import configurations
import sys
import logging
import base64
from datetime import datetime
from caqfmodel.utils import caqfsql
from caqfmodel.utils import caqfconstants

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("test")

def replace_all(text, dic):
    for i, j in dic.iteritems():
        if j is None:
            text = text.replace(str(i), "")
        else:
            text = text.replace(str(i), str(j))
    return text  

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger("test")

print sys.argv
print sys.argv[1:]
args = sys.argv[1:]


wlf_props_file_path=args[0]
job_id=args[1]
num_executors=args[2] 
executor_memory=args[3]
executor_cores=args[4]

conf = (SparkConf()
         .setAppName("CAQF Model Test")
         .set("spark.dynamicAllocation.enabled",False) 
         .set("spark.executor.instances",num_executors)    
         .set("spark.python.worker.memory","3g")         
         .set("spark.executor.memory",executor_memory)
         .set("spark.driver.memory","3g")
         .set("spark.executor.cores",executor_cores)
         .set("spark.executor.heartbeatInterval","3600s")
         .set("spark.yarn.executor.memoryOverhead","1024m")
         )
 
sc = SparkContext(conf = conf)
sqlContext = SQLContext(sc)
hiveContext = HiveContext(sc)

job_id=21521

# list files in current application directory
print "cwd",os.getcwd()

files = [f for f in os.listdir('.')]
for f in files:
    logger.info(f)

scripts_dir='./scripts'
print scripts_dir
if os.path.exists(scripts_dir):
    files = [f for f in os.listdir(scripts_dir)]
    for f in files:
        logger.info(f)
            
'''
config={
        "lifecycle" : "_dev1" # config["lifecycle"]
        ,"scripts_path" : scripts_path
        ,"impala_database_name" : "wlf_model_dev1" #config["impala_database_name"]
        ,"impalaConnection" : "impala-shell --ssl -ku zswlfa1d@CORP.BANKOFAMERICA.COM -i dhas-imp.bankofamerica.com" #config["impalaConnection"]
        ,"impala_retry_count" : 3 #config["impala_retry_count"]
        ,"realm" : "@CORP.BANKOFAMERICA.COM" #config["realm"]
        ,"request_pool" : "root.haasimmwldev" #config["request_pool"]
        ,"output_path_table" :  output_path_table #config['output_path_table']
        ,"model_db_name" : "wlf_model_dev1" #config['model_db_name']
        ,"impala_database_superset" : "wlf_superset_dev1" #config["impala_database_superset"]
    }

configs={
        'oracle_schema':oracle_schema
        ,'oracle_password':oracle_password
        ,'driver':constants.ORCL_DRIVER
        ,'oracle_url' : oracle_url    
        ,'region' : '_dev1'
        ,'db_name' : 'wlf_dev${region}'
        ,'test' : 123
    }
'''


wlf_propsdict = utils.load_properties(wlf_props_file_path, "=", "#")
region=wlf_propsdict['region']
nameNode=wlf_propsdict['nameNode']
wlf_base_dir=wlf_propsdict['wlf_base_dir']
for k, v in wlf_propsdict.items():
    wlf_propsdict[k] = v.replace("${region}",region).replace("${nameNode}",nameNode).replace("${wlf_base_dir}",wlf_base_dir)
        
oracle_url = wlf_propsdict["oracle_url"]
logger.info("oracle_url:"+oracle_url)
oracle_schema = wlf_propsdict["oracle_schema"]
logger.info("oracle_schema:"+oracle_schema)
oracle_password = base64.decodestring(wlf_propsdict["oracle_password"])

model_job = configurations.get_model_job(sc,job_id,oracle_url, oracle_schema, oracle_password)
create_date = model_job[constants.MODEL_JOB_CREATE_DATE]
logger.info(type(create_date))
logger.info("MODEL_TYPE:"+model_job[constants.MODEL_JOB_MODEL_TYPE])
model_configs = configurations.load_configurations(sc,model_job[constants.MODEL_JOB_MODEL_TYPE],oracle_url, oracle_schema, oracle_password)
wlf_propsdict.update(model_configs)
configs=wlf_propsdict

#oracleutility.update_status_oracle(sc,sqlContext,int(job_id), 'CRELL Running Model', 'SPARK_SYSTEM', str(datetime.now()), int(4), 'SPARK::Running Model: ',configs)
        
#scripts_path=configs["scripts_path"]
write_partition=configs["output"]["write_repartition"]
scripts_path='.'
lifecycle=configs["region"]
impala_database_name=configs["hive_model_db"]
#impalaConnection=configs["impalaConnection"]
#impala_retry_count=configs["impala_retry_count"]
impalaConnection="impala-shell --ssl -ku zswlfa1d@CORP.BANKOFAMERICA.COM -i dhas-imp.bankofamerica.com"
impala_retry_count=3
realm=configs["impala_realm"]
request_pool=configs["request_pool"]
output_path_table=configs['data_dir']
model_db_name=configs['hive_model_db']
impala_database_superset=configs["hive_ss_db"]
name_node=configs["nameNode"]
moveIntermediateTables_script=scripts_path + "/moveIntermediateTables.sh"
generateRunOrrFrrMappingSQL_script=scripts_path + "/generateRunOrrFrrMappingSQL.sh"
generateRunSegmentedReportsSQL_script=scripts_path + "/generateRunSegmentedReportsSQL.sh"
ret_code=0

#job_id=model_job[constants.MODEL_JOB_ID]

timer_start = datetime.now()
hive_job_id=21522
hive_period_id='20160331'
model_results_path =  output_path_table +'/' + model_db_name     
logger.info("writing aq_el_segmented start " + str(timer_start))
caqf_aq_el_segment = {"hive_model_db": model_db_name, "hive_ss_db": impala_database_superset, "jobId": hive_job_id, "periodId" : hive_period_id}
caqf_aq_el_segment_output = replace_all(caqfsql.AQ_EL_SEGMENTED_NEW, caqf_aq_el_segment)
logger.info(caqf_aq_el_segment_output)
aq_el_segmented_df=hiveContext.sql(caqf_aq_el_segment_output)
aq_el_segmented_coalesce = aq_el_segmented_df.coalesce(int(write_partition))
aq_el_segmented_coalesce.write.mode('overwrite').parquet(model_results_path + '/aq_el_segmented/job_id=' + str(hive_job_id))
hiveContext.sql("ALTER TABLE wlf_model_dev1" + caqfconstants.CAQF_AQ_EL_SEGMENTED + " ADD IF NOT EXISTS PARTITION (job_id=" + str(hive_job_id) + ")")
timer_end =  datetime.now()
logger.info("writing aq_el_segmented done " + str(timer_end) + " " +  str(timer_end-timer_start))
    
timer_start = datetime.now()
logger.info("writing segmented_rpt start " + str(timer_start))
caqf_segmented_rpt = {"hive_model_db": model_db_name, "hive_ss_db": impala_database_superset, "jobId": hive_job_id}
caqf_segmented_rpt_output = replace_all(caqfsql.SEGMENTED_RPT_NEW, caqf_segmented_rpt)
caqf_segmented_rpt_df=hiveContext.sql(caqf_segmented_rpt_output)
caqf_segmented_rpt_coalesce = caqf_segmented_rpt_df.coalesce(int(write_partition))
caqf_segmented_rpt_coalesce.write.mode('overwrite').parquet(model_results_path + '/segmented_rpt/job_id=' + str(hive_job_id))
hiveContext.sql("ALTER TABLE " + configs['hive_model_db'] + "" + caqfconstants.CAQF_SEGMENTED_RPT + " ADD IF NOT EXISTS PARTITION (job_id=" + str(hive_job_id) + ")")
timer_end =  datetime.now()
logger.info("writing segmented_rpt done " + str(timer_end) + " " + str(timer_end-timer_start))
    
# refactor to use common hdfs_datafiles path 
model_job_output_path =  output_path_table +'/' + model_db_name + "/model_job"          
#logger.info("writing to " + model_job_output_path)
#model_job_util.insert_model_job(sc, sqlContext, model_job, model_job_output_path) 
#logger.info("done writing to " + model_job_output_path)

map_input_records=0
numQrts=int(model_job[constants.MODEL_JOB_NUM_OF_PERIODS])
numRatings=6
model_type=model_job[constants.MODEL_JOB_MODEL_TYPE]

refreshtable=scripts_path + "/refreshTables.sh"
logger.info(refreshtable)

cmd_args=(refreshtable,
          impala_database_name,
          impalaConnection,
          realm,
          request_pool)
ret_code=utils.run_process(cmd_args) 
print(ret_code)

    