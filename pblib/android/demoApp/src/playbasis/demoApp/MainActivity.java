package playbasis.demoApp;

import java.io.IOException;

import playbasis.pblib.Playbasis;

import android.app.Activity;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.JsonReader;
import android.util.JsonToken;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.TextView.OnEditorActionListener;

public class MainActivity extends Activity
{
	public static final int MAX_OUTPUT_CHARS = 512;
	
	private InputProcessor inputProcessor;
	public static Playbasis playbasis;

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);

		// set the action listener for the enter key
		inputProcessor = new InputProcessor(this);
		EditText inputBox = (EditText) findViewById(R.id.inputBox);
		assert inputBox != null;
		inputBox.setOnEditorActionListener(inputProcessor);
		
		playbasis = new Playbasis();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu)
	{
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.activity_main, menu);
		return true;
	}

	// process the input and display to the output box
	public void submitClicked(View view)
	{
		EditText inputBox = (EditText) findViewById(R.id.inputBox);
		assert inputBox != null;
		String message = inputBox.getText().toString();

		AsyncWebRequestTask requestTask = new AsyncWebRequestTask(this);
		requestTask.execute(message);
		message = "Processing Request: " + message;

		TextView outputBox = (TextView) findViewById(R.id.outputBox);
		assert outputBox != null;
		String output = outputBox.getText().toString();

		output = message + "\n" + output;
		if (output.length() > 256)
			output = output.substring(0, 256);
		outputBox.setText(output);
		inputBox.setText("");
	}
}

class InputProcessor implements OnEditorActionListener
{
	private MainActivity mainActivity;

	public InputProcessor(MainActivity activity)
	{
		mainActivity = activity;
	}

	@Override
	public boolean onEditorAction(TextView v, int actionId, KeyEvent event)
	{
		boolean enterPressed = event.getAction() == KeyEvent.ACTION_DOWN && event.getKeyCode() == KeyEvent.KEYCODE_ENTER;
		if (!enterPressed || (actionId != 0 && actionId != EditorInfo.IME_ACTION_DONE
											&& actionId != EditorInfo.IME_ACTION_GO
											&& actionId != EditorInfo.IME_ACTION_NEXT 
											&& actionId != EditorInfo.IME_ACTION_SEND))
			return false; // skip event that we don't care about

		// process the input
		assert mainActivity != null;
		mainActivity.submitClicked(v);
		return true;
	}
}

class AsyncWebRequestTask extends AsyncTask<String, Void, String>
{
	private MainActivity mainActivity;

	public AsyncWebRequestTask(MainActivity activity)
	{
		mainActivity = activity;
	}

	String printJsonReader(JsonReader reader)
	{
		StringBuilder str = new StringBuilder();	
		try
		{
			while(true)
			{
				JsonToken nextToken = reader.peek();
				if(nextToken == JsonToken.BEGIN_OBJECT)
				{
					reader.beginObject();
					str.append("{\n");
				}
				else if(nextToken == JsonToken.END_OBJECT)
				{
					reader.endObject();
					str.append("}\n");
				}
				else if(nextToken == JsonToken.BEGIN_ARRAY)
				{
					reader.beginArray();
					str.append("[\n");
				}
				else if(nextToken == JsonToken.END_ARRAY)
				{
					reader.endArray();
					str.append("]\n");
				}
				else if(nextToken == JsonToken.NAME)
				{
					str.append(reader.nextName());
					str.append("=");
				}
				else if(nextToken == JsonToken.STRING)
				{
					str.append(reader.nextString());
					str.append("\n");
				}
				else if(nextToken == JsonToken.BOOLEAN)
				{
					str.append(reader.nextBoolean());
					str.append("\n");
				}
				else if(nextToken == JsonToken.NULL)
				{
					reader.nextNull();
					str.append("null\n");
				}
				else if(nextToken == JsonToken.NUMBER)
				{
					str.append(reader.nextLong());
					str.append("\n");
				}
				else if(nextToken == JsonToken.END_DOCUMENT)
				{
					reader.close();
					return str.toString();
				}
				else
				{
					reader.close();
					return "invalid json";
				}
			}
		}
		catch(IOException e)
		{
			return null;
		}
	}
	
	@Override
	protected String doInBackground(String... args)
	{
		String req = args[0];
		String[] input = req.split("\\s+");
		if(input.length <= 0)
			return null;
		if(input[0].equals("auth"))
			return (Playbasis.instance.auth(input[1], input[2])) ? "success" : "failed";
		else if(input[0].equals("player"))
			return printJsonReader( Playbasis.instance.player(input[1]));
		else if(input[0].equals("register"))
			return printJsonReader( Playbasis.instance.register(input[1], input[2], input[3], input[4], input[5], input[6]));
		return null;
	}

	@Override
	protected void onPostExecute(String result)
	{
		TextView outputBox = (TextView) mainActivity
				.findViewById(R.id.outputBox);
		assert outputBox != null;
		String output = outputBox.getText().toString();

		output = result + "\n" + output;
		if (output.length() > MainActivity.MAX_OUTPUT_CHARS)
			output = output.substring(0, MainActivity.MAX_OUTPUT_CHARS);
		outputBox.setText(output);
	}
}
